#!/usr/bin/env bash
# Builds database/clean.sql: the starting database for a new client's Sales CRM.
#   - every table, empty (structure only, counters start at 1)
#   - reference data only: countries, states, phone codes, roles, lists (Lists page), lead sources and
#     statuses, reference-number counters (at 0) and the record of applied migrations
#   - no leads, records, users, logs, sessions or Integrations settings
# Usage (from the CRM folder):  bash database/make_clean_sql.sh [database] [mysqldump] [mysql user]
# Defaults: crm7strokes, mysqldump on PATH, root. Run it against an up-to-date database (migrations applied).
set -euo pipefail
DB="${1:-crm7strokes}"
DUMP="${2:-mysqldump}"
USER_="${3:-root}"
OUT="$(cd "$(dirname "$0")" && pwd)/clean.sql"

# Tables whose rows are copied. Everything else is created empty.
REF="countries states country_code ci_roles ci_terms ci_sources ci_status ci_reference_sequences schema_migrations"

OPTS="-u $USER_ --skip-comments --skip-dump-date --single-transaction --skip-triggers --no-tablespaces"
{
  echo "-- Sales CRM: clean starting database (structure + reference data only)."
  echo "-- Made by database/make_clean_sql.sh. Load into an empty database, then run: php migration_runner.php"
  echo "SET NAMES utf8mb4;"
  echo "SET SESSION innodb_strict_mode = 0;"
  echo "SET FOREIGN_KEY_CHECKS = 0;"
  echo "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';"
  echo
  # Structure of every table except phpMyAdmin's own; counters reset so ids start at 1
  "$DUMP" $OPTS --no-data --skip-add-drop-table "$DB" $(echo "SHOW TABLES" | "${DUMP%dump*}" -u "$USER_" -N "$DB" | grep -v '^pma__') \
    | sed -E 's/ AUTO_INCREMENT=[0-9]+//'
  echo
  # Reference rows
  "$DUMP" $OPTS --no-create-info --skip-extended-insert --order-by-primary "$DB" $REF
  echo
  echo "UPDATE ci_reference_sequences SET current_val = 0;"
  echo "SET FOREIGN_KEY_CHECKS = 1;"
} > "$OUT"
# Development traces must never reach a client copy
if grep -n -i -E "INSERT INTO \`(ci_user|ci_leads|ci_boleads|ci_integration_settings|ci_sessions)\`" "$OUT"; then
  echo "clean.sql holds rows it must not hold; stopped." >&2; rm -f "$OUT"; exit 1
fi
echo "Wrote $OUT ($(grep -c '^CREATE TABLE' "$OUT") tables)"
