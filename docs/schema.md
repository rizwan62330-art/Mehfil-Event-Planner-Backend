# Task 2 database schema

```mermaid
erDiagram
  USERS ||--o{ LISTINGS : owns
  USERS ||--o{ REVIEWS : writes
  CATEGORIES ||--o{ LISTINGS : contains
  LISTINGS ||--o{ REVIEWS : receives
  USERS {
    bigint id PK
    varchar name
    varchar email UK
    varchar password
    varchar role
  }
  CATEGORIES {
    bigint id PK
    varchar name UK
    varchar slug UK
  }
  LISTINGS {
    bigint id PK
    bigint user_id FK
    bigint category_id FK
    varchar name
    varchar slug UK
    varchar city
    integer capacity
    decimal starting_price
    boolean is_claimed
  }
  REVIEWS {
    bigint id PK
    bigint user_id FK
    bigint listing_id FK
    tinyint rating
    text comment
    boolean is_approved
  }
```

- Each venue listing belongs to a category and to an owner/user. Ownership is initial seeded attribution, **not proof of a verified claim**; that flow is a later task.
- Each review belongs to one user and one venue. The `(listing_id, user_id)` unique constraint permits at most one review per user per venue.
- Restrict deleting owners/categories while listings reference them; delete a listing's reviews when the listing is deleted.
- `reviews.rating` must be validated within 1–5 in Task 3; database uses unsigned tinyint and does not independently enforce the 1–5 upper bound.
- Illustrative venue names and addresses are fictitious demo data generated/seeded for development, not verified businesses.
