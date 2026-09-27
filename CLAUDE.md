# CLAUDE.md

@AGENTS.md

`AGENTS.md` holds generic Symfony conventions. This file holds the project-specific
decisions. **Where the two conflict, this file wins.**

## Project: Food SM

A food app built around collecting each user's food tastes. Long-term it has four
parts: taste collection, a social layer (compare tastes with friends), meal planning,
and a dinner/event organizer that suggests recipes everyone at the table will like.

Core data model: every user has a numeric score from 0 to 10 for every ingredient.
Explicit ratings (swipes) and, later, implicit signals (recipe interactions) both
feed that score. All data is public for now.

## MVP scope

Goal: prove that the swipe interface works and that the taste data is useful.
Keep it minimal but complete.

### Data

| Table | Purpose | Notes |
|---|---|---|
| `users` (Identity) | Test users | 2–3 hardcoded accounts, no auth |
| `ingredients` (TasteCollection) | Items to rate and search | 250–500 rows, LLM-generated CSV; fields below |
| `user_ingredient_scores` (TasteCollection) | One score per (user, ingredient) | Created on first rating, updated afterwards; score 0–10; `user_id` is a plain `UserId`, not checked against Identity |

Ingredient fields:

| Field | Type | In CSV | Notes |
|---|---|---|---|
| `id` | int, autoincrement | no | |
| `name` | string, unique | yes | English, singular, lowercase (`shiitake mushroom`); what search matches on |
| `category` | enum | yes | vegetable, fruit, meat, fish_seafood, dairy_egg, grain_cereal, legume, nut_seed, herb_spice, condiment_sauce, fat_oil, sweetener, fungi, other |
| `region` | string, nullable | yes | Cuisine it's most associated with (`East Asia`); null if universal; unused in the MVP |
| `image_url` | string, nullable | no | Column exists; no images are loaded yet |
| `created_at` | datetime | no | |

### API (JSON, all under `/api`)

```
POST /api/users/{userId}/ingredients/{ingredientId}/rate   body: { "score": 0-10 }  → upsert the score
GET  /api/users/{userId}/ingredients                         → user's top 5 ingredients by score
GET  /api/ingredients                                        → all ingredients (swipe / search)
GET  /api/ingredients/{ingredientId}                         → ingredient + current user's score + target user's score
```

### Frontend (separate, React + TypeScript)

Swipe page, profile (top 5), search by name, two-user side-by-side comparison.
Readable and mobile-friendly; no styling work beyond that.

### Out of scope for the MVP

Do not build these, even partially: authentication, friending or privacy controls,
region filtering (the `region` field exists but isn't used), recipes, meal planning,
event organization.

### Open questions (ask, don't decide)

- Whether the comparison endpoint (`GET /api/ingredients/{id}` with two users' scores)
  lives in TasteCollection or in Social. The principle below says comparison is
  Social's job; the current URL shape suggests TasteCollection.

## Architecture: modular monolith

One Symfony app, structured so a module could later become a separate service without
a rewrite. Not microservices: one process, one database.

### Modules

| Module | Owns | Status |
|---|---|---|
| `Identity` | Users (names and profile info) | MVP |
| `TasteCollection` | Ingredients, users' scores, rating | MVP |
| `Social` | Viewing and comparing users' profiles and scores | MVP |
| `MealPlanning`, `EventHelper` | – | Don't create until work on them actually starts |
| `Shared` | Generic value objects only (e.g. `UserId`) | – |

Other modules refer to users only by `Shared\Domain\UserId`. Anything more (names,
profile info) comes from Identity's API.

### Layout and layers

```
src/<Module>/
  Domain/          plain PHP: entities (Domain/Model), value objects, domain events (Domain/Event), repository interfaces
  Application/     use cases, the module's public API interface (e.g. TasteCollectionApi), DTOs
  Infrastructure/  adapters: Http/Controller, Persistence/Doctrine (repositories + XML mapping), framework glue
src/Shared/Domain/ generic value objects only
```

Namespaces: `App\<Module>\<Layer>\...` (the single `App\` → `src/` PSR-4 root).

### Rules

1. **Each module is a vertical slice**: its own domain logic, data access and HTTP
   endpoints. Don't split by technical concern across modules.
2. **Dependencies point inward**: Domain → Shared only. Application → its own Domain,
   Shared. Infrastructure → its own Application and Domain, Shared, Symfony and Doctrine.
   **Domain and Application never import `Symfony\` or `Doctrine\`.**
3. **Cross-module calls go only through the target module's Application-layer API
   interface.** Never touch another module's Domain or Infrastructure. The only
   allowed edges right now: `Social\Application` → `TasteCollection\Application` and
   `Social\Application` → `Identity\Application`.
4. **Each module owns its tables.** No module queries another module's tables, and
   there are no foreign keys across modules, even though they share one database.
5. **No shared domain model.** When a module needs a concept it doesn't own, it
   defines its own minimal read model of it and gets the data through the owner's API.
6. **Blocking calls that need a return value** go through the API interface.
   **Side effects that don't block the caller** (cache invalidation, stats,
   notifications) go through domain events on Symfony's in-process event dispatcher.
   Put a dispatcher interface in Application and implement it in Infrastructure.
7. **Design APIs to be coarse**: batch-friendly methods (e.g. scores for many
   ingredients at once), not chatty per-item calls, so they still make sense if a
   module ever sits behind a network.

`deptrac.yaml` enforces rules 2 and 3. **Run `composer deptrac` after every change
that adds a `use` statement across layers or modules; a violation is a bug, not
something to whitelist.** Changing `deptrac.yaml` itself needs explicit approval.

### Persistence

- Doctrine ORM with **XML mapping**, stored in
  `src/<Module>/Infrastructure/Persistence/Doctrine/Mapping/<ShortClassName>.orm.xml`.
  Domain entities have **no ORM attributes**. This overrides AGENTS.md's
  "prefer attributes" for entity mapping.
- **Don't use `make:entity`**: it generates attribute-mapped entities in `src/Entity`.
  Hand-write the domain class and its XML mapping, and add a new module's mapping
  block to `config/packages/doctrine.yaml`.
- Repository interfaces live in Domain. Doctrine implementations live in
  Infrastructure/Persistence/Doctrine.
- Migrations: one folder (`migrations/`), but each migration touches **one module's
  tables only** and its description starts with the module name, e.g.
  `[TasteCollection] create ingredients table`. Generate them with `make:migration`,
  then apply them with `doctrine:migrations:migrate`.
- Validation (`#[Assert\...]`) goes on request DTOs in Infrastructure/Http, not on
  domain entities. The domain enforces its own invariants in plain PHP (e.g. a
  `Score` value object that rejects values outside 0–10).

### Wiring

- Every class in `src/` is autowired, except `*/Domain/Model/`, `*/Domain/Event/` and
  `Shared/Domain/`.
- An API interface with one implementation is aliased automatically. Add an explicit
  alias (`#[AsAlias]`) only when a second implementation appears.
- Routes: each module's `Infrastructure/Http/Controller/` is loaded in
  `config/routes.yaml` with the `/api` prefix, and routes are declared with `#[Route]`.
  A new module needs its own entry there.

## Stack and environment

- PHP ≥ 8.4, Symfony 8.x, Doctrine ORM 3, SQLite.
- Development happens in WSL. The SQLite file lives outside the project, on the
  Linux filesystem: `DATABASE_URL` is overridden in `.env.local` to point to
  `~/.local/share/food-sm/data_<env>.db`. SQLite needs no `doctrine:database:create`;
  the file is created on first connection.
- Answers to AGENTS.md's "ask before generating" questions:
  persistence is Doctrine ORM (XML mapping); the interface is a JSON API only (no Twig);
  there is no auth in the MVP.

## Checks before calling something done

```bash
php bin/console lint:container
composer deptrac
php bin/phpunit            # once symfony/test-pack is installed
```

## Commit messages

[Conventional Commits 1.0](https://www.conventionalcommits.org):

```
<type>(<scope>)<!>: <description>

<body>

<footers>
```

### Type

| Type | Use for |
|---|---|
| `feat` | New or changed behaviour a caller can see: endpoint, use case, field, UI screen |
| `fix` | A bug fix |
| `perf` | A performance improvement with identical behaviour |
| `refactor` | Any other code change with identical behaviour |
| `style` | Formatting only (php-cs-fixer, Prettier), no code change |
| `test` | Tests added or changed on their own |
| `docs` | Documentation only: `CLAUDE.md`, `AGENTS.md`, READMEs, comments |
| `build` | Dependencies and build setup: `composer`/`npm` changes (with the recipe files they generate), Vite/TypeScript config |
| `ci` | CI pipelines |
| `chore` | Everything else that isn't shipped code: dev tooling config (deptrac, PHPStan, php-cs-fixer, editorconfig), scaffolding, fixtures and seed data |
| `revert` | Reverting an earlier commit (see below) |

When two types fit, take the first one in this table. A change is one type: a
`feat` commit includes its tests, its migration and XML mapping, and any
`CLAUDE.md` update that describes it. `test` and `docs` are only for commits that
contain nothing else.

### Scope

The part of the repository the change lives in, spelled as its directory:

- a backend module: `Identity`, `TasteCollection`, `Social`, `Shared`
  (`src/<Module>/`, and that module's migrations, config blocks and tests);
- `frontend` for the React app (`frontend/`).

Leave the scope out when a change spans several of these or touches none of them
(root config, `deptrac.yaml` as a whole, `CLAUDE.md` sections that aren't about one
module).

A feature that needs both an endpoint and a screen is **two commits**: the backend
one first, then the `frontend` one. Each migration touches one module's tables, so a
commit never adds migrations for two modules.

### Description

- Imperative mood (`add`, not `added`/`adds`), no final period.
- Starts with a lowercase letter; identifiers keep their casing
  (`batch score lookups through TasteCollectionApi`).
- The whole header, type and scope included, fits in 72 characters.
- Says what changes for someone reading the log, not which files moved.

### Body and footers

- The body is optional. Write one when the *why* isn't obvious from the diff:
  a decision, a rejected alternative, a constraint. Wrap at 72 characters.
- Footers use git trailer syntax (`Token: value`), one per line, after a blank
  line. `Co-Authored-By:` goes here.
- **Breaking changes**: add `!` before the colon **and** a `BREAKING CHANGE:`
  footer explaining the change and what callers must do. This is required whenever
  an `/api` contract changes (path, method, request body, response shape, status
  codes), because the frontend depends on it. The frontend commit that adapts to it
  follows right after.
- **Reverts**: `revert: <header of the reverted commit>`, with the body
  `This reverts commit <sha>.` and a line on why.

### Granularity

- **One logical change per commit.** If the description needs "and" to join two
  unrelated things, split it.
- Every commit leaves the repo working: `lint:container`, `composer deptrac` and
  `php bin/phpunit` pass (and the frontend builds, once it exists).
- Don't add version or maintenance-type tags (`[minor]`, `[évolutive]`, …).
  Versioning, if ever needed, is derived from the types and `!`.

### Examples

```
feat(TasteCollection): add score upsert endpoint
feat(frontend): add swipe page
fix(Identity): return 404 for unknown user id
perf(TasteCollection): load top ingredients in a single query
refactor(Social): batch score lookups through TasteCollectionApi
style: apply php-cs-fixer @Symfony ruleset
build: add symfony/lock
build(frontend): upgrade vite to 7
chore: allow Social to depend on Identity in deptrac
chore(TasteCollection): add ingredients CSV fixture
docs: record ingredient source decision
```

```
feat(TasteCollection)!: return top ingredients as objects

The profile page needs names alongside scores; returning ids forced
one extra request per ingredient.

BREAKING CHANGE: GET /api/users/{userId}/ingredients now returns
[{ id, name, score }] instead of a list of ids.
```
