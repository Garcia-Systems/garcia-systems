# Laravel Cloud structured content runbook

Garcia Systems structured content is installed with reversible Artisan commands, not production seeders. Do **not** run `DatabaseSeeder` or `StarterPublicContentSeeder` in production for this workflow.

## Feature flags

Data installation and public visibility are separate controls. These flags can hide public features without deleting data:

```env
FEATURE_AI_ASSESSMENT=true
FEATURE_OPPORTUNITY_ATLAS=true
FEATURE_OPPORTUNITY_EXPLORER=true
```

Disabling a feature removes it from public navigation and direct public routes return an unavailable/not found response. Re-enable the flag to restore access without reinstalling content.

## Safe production workflow

1. Verify the Laravel Cloud managed MySQL backup is current.
2. Deploy code and confirm CI/deployment succeeds.
3. Preview assessment installation:

   ```bash
   php artisan garcia:content install --dataset=assessment --dry-run
   ```

4. Install assessment:

   ```bash
   php artisan garcia:content install --dataset=assessment
   ```

5. Record the returned run identifier.
6. Test the assessment manually.
7. Preview Atlas installation:

   ```bash
   php artisan garcia:content install --dataset=atlas --dry-run
   ```

8. Install Atlas only if approved:

   ```bash
   php artisan garcia:content install --dataset=atlas
   ```

9. Inspect runs:

   ```bash
   php artisan garcia:content status
   php artisan garcia:content status --run=<run-id>
   ```

10. Preview rollback before changing data:

   ```bash
   php artisan garcia:content rollback --run=<run-id> --dry-run
   ```

11. Roll back a run if necessary:

   ```bash
   php artisan garcia:content rollback --run=<run-id>
   ```

   The latest completed run can also be rolled back with:

   ```bash
   php artisan garcia:content rollback --latest
   ```

## Safety notes

- Rollback uses the recorded installation manifest; it does not guess from slugs.
- Pre-existing lookup records are reused and never deleted by rollback.
- Pivot relationships are detached only when the installation created them.
- Assessment questions created by a run are deleted only when they have no historical responses. Referenced questions are deactivated so historical assessments remain valid.
- Atlas lookup records created by a run are retained when later referenced by manual or external content.
- Articles, videos, contacts, leads, and administrators are not installed, modified, or removed by these commands.
- After real users interact with a feature, disabling it through configuration is safer than deleting data.
