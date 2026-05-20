# RTH Modern: Setup, Launch, and Legacy Migration Guide

This document provides complete instructions for downloading, running, and migrating legacy data into the modernized **RTH Modern** platform.

---

## Part 1: Downloading & Running RTH Modern

New users or developers can set up the entire workspace environment using Docker and Git.

### 1. Clone the Repository
Clone the codebase directly from GitHub and navigate into the project directory:
```bash
git clone https://github.com/tonyclarke999-wq/rth.git rth-modern
cd rth-modern
```

### 2. Start the Docker Containers
Due to a configuration mismatch between the default Symfony recipe (`compose.override.yaml`) and the customized container definitions in `docker-compose.yml`, you must explicitly point Docker Compose to the main configuration file:
```bash
docker compose -f docker-compose.yml up -d
```
This launches all five required services in the background:
* **`app`**: The RTH Modern Symfony application.
* **`db`**: The PostgreSQL database.
* **`legacy`**: The Legacy PHP application running side-by-side.
* **`mailhog`**: The local SMTP mail server and testing UI.
* **`pgadmin`**: The database administration interface.

### 3. Install PHP & Symfony Dependencies
Install all backend packages inside the running container using Composer:
```bash
docker compose exec app composer install
```

### 4. Run Database Migrations
Create the database tables and set up the schema by running the Doctrine migrations:
```bash
docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction
```

### 5. Access the Services
Once running, the application and utility tools are accessible via your browser:

| Service | Address | Description |
| :--- | :--- | :--- |
| **RTH Modern** | [http://localhost:8081](http://localhost:8081) | The modernized interface. |
| **RTH Legacy** | [http://localhost:8082](http://localhost:8082) | The original legacy platform. |
| **Mailhog Web UI** | [http://localhost:8025](http://localhost:8025) | View outgoing emails sent by the platform. |
| **pgAdmin 4** | [http://localhost:5050](http://localhost:5050) | DB management UI (Login: `admin@admin.com` / `admin`). |

---

## Part 2: Migrating Legacy RTH Data

If you have legacy database records that you want to move into RTH Modern, follow these steps:

### 1. Prerequisites: Exporting Legacy Data
1. Export the database of your legacy RTH instance into a standard SQL dump named **`rth.sql`**.
2. Locate the legacy directory in the RTH Modern structure: `rth/legacy/sql/`.
3. Place the SQL dump inside that folder so it resides at:
   ```text
   rth/legacy/sql/rth.sql
   ```
   *(Note: Inside the Docker container, this maps to `/var/www/html/legacy/sql/rth.sql`)*.

### 2. Running the Migration Command
RTH Modern includes a dedicated Symfony console command `app:import-legacy` to parse the SQL dump and write the translated entities to the database.

* **Using Docker (Recommended):**
  ```bash
  docker compose exec app php bin/console app:import-legacy
  ```
* **Using a Local PHP Installation:**
  ```bash
  php bin/console app:import-legacy
  ```

### 3. What the Migration Command Does
The `ImportLegacyDataCommand` processes the legacy SQL dump file and maps legacy schema tables to RTH Modern's Doctrine Entities:

* **Users (`user` table):**
  * Extracts username, first name, last name, and email address.
  * Hashes and assigns a default password (`password`) using Symfony's `UserPasswordHasherInterface`.
  * Assigns the `ROLE_ADMIN` role to the legacy `admin` user, and `ROLE_USER` to all other users.
* **Projects (`project` table):**
  * Imports project names and descriptions.
  * Preserves association maps to correctly link requirements and test suites.
* **Requirements (`requirement` table):**
  * Imports requirements and associates them with their corresponding projects using mapped legacy project IDs.
  * Defaults requirement statuses to `'New'` and populates standard descriptions.
* **Test Suites (`testset` table):**
  * Imports test suites (referred to as test sets in legacy RTH) and maintains their original project associations, names, and descriptions.

### 4. Post-Migration Recommendations
1. **Reset Passwords:** Because all migrated users are assigned the default password `password` for security, users should be prompted to change their passwords upon their first login.
2. **Verify Entity Associations:** Check the dashboard or database to ensure all requirements and test suites are successfully linked under their respective projects.
