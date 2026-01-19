# Branching Strategy and Naming Conventions

## Overview

This document outlines the Git branching strategy and naming conventions for the Cross-Browser Clipboard System project. Following these guidelines ensures consistent collaboration, clear code organization, and streamlined deployment processes.

## Branch Types

### Main Branches

#### `main`
- **Purpose**: Production-ready code
- **Protection**: Protected branch, requires pull request reviews
- **Deployment**: Automatically deploys to production
- **Merge From**: `release/*` branches only
- **Direct Commits**: Not allowed

#### `develop`
- **Purpose**: Integration branch for features
- **Protection**: Protected branch, requires pull request reviews
- **Deployment**: Automatically deploys to staging environment
- **Merge From**: `feature/*`, `bugfix/*`, `hotfix/*` branches
- **Direct Commits**: Not allowed (except for minor documentation updates)

### Supporting Branches

#### Feature Branches (`feature/*`)
- **Purpose**: Develop new features or enhancements
- **Naming Convention**: `feature/<issue-number>-<short-description>`
- **Branch From**: `develop`
- **Merge Into**: `develop`
- **Lifetime**: Temporary, deleted after merge
- **Examples**:
  - `feature/123-user-authentication`
  - `feature/456-clipboard-notifications`
  - `feature/789-api-integration`

#### Bugfix Branches (`bugfix/*`)
- **Purpose**: Fix bugs found in development/staging
- **Naming Convention**: `bugfix/<issue-number>-<short-description>`
- **Branch From**: `develop`
- **Merge Into**: `develop`
- **Lifetime**: Temporary, deleted after merge
- **Examples**:
  - `bugfix/234-login-validation-error`
  - `bugfix/567-session-timeout-issue`
  - `bugfix/890-file-upload-crash`

#### Hotfix Branches (`hotfix/*`)
- **Purpose**: Critical fixes for production issues
- **Naming Convention**: `hotfix/<version>-<short-description>`
- **Branch From**: `main`
- **Merge Into**: Both `main` AND `develop`
- **Lifetime**: Temporary, deleted after merge
- **Examples**:
  - `hotfix/1.2.1-security-patch`
  - `hotfix/1.2.2-database-connection`
  - `hotfix/1.2.3-csrf-token-fix`

#### Release Branches (`release/*`)
- **Purpose**: Prepare for production release
- **Naming Convention**: `release/<version>`
- **Branch From**: `develop`
- **Merge Into**: Both `main` AND `develop`
- **Lifetime**: Temporary, deleted after merge
- **Examples**:
  - `release/1.0.0`
  - `release/1.1.0`
  - `release/2.0.0`

#### Experimental Branches (`experiment/*`)
- **Purpose**: Proof of concepts, experiments, spikes
- **Naming Convention**: `experiment/<short-description>`
- **Branch From**: `develop`
- **Merge Into**: `develop` (if successful) or discarded
- **Lifetime**: Temporary
- **Examples**:
  - `experiment/websocket-implementation`
  - `experiment/redis-caching`
  - `experiment/graphql-api`

## Branch Naming Conventions

### General Rules

1. **Use lowercase letters only**
2. **Use hyphens (-) to separate words**, not underscores or spaces
3. **Keep names short but descriptive** (max 50 characters)
4. **Include issue/ticket number** when applicable
5. **Use present tense verbs** for actions

### Format Structure

```
<type>/<issue-number>-<short-description>
```

**Components**:
- `<type>`: Branch type (feature, bugfix, hotfix, release, experiment)
- `<issue-number>`: GitHub/Jira issue number (optional but recommended)
- `<short-description>`: Brief description using kebab-case

### Valid Examples

✅ **Good**:
- `feature/123-add-email-notifications`
- `bugfix/456-fix-session-expiry`
- `hotfix/1.2.1-security-vulnerability`
- `release/2.0.0`
- `experiment/real-time-sync`

❌ **Bad**:
- `Feature/Add_Email_Notifications` (wrong case, underscores)
- `fix-bug` (missing type prefix)
- `feature/add-some-new-stuff-to-the-app` (too vague)
- `john-working-on-feature` (personal names)
- `temp` (not descriptive)

## Workflow Examples

### Feature Development Workflow

```bash
# 1. Create feature branch from develop
git checkout develop
git pull origin develop
git checkout -b feature/123-clipboard-sharing

# 2. Work on feature, commit regularly
git add .
git commit -m "feat: add clipboard sharing functionality"

# 3. Keep branch updated with develop
git fetch origin
git rebase origin/develop

# 4. Push to remote
git push origin feature/123-clipboard-sharing

# 5. Create Pull Request to develop
# (via GitHub/GitLab UI)

# 6. After merge, delete branch
git checkout develop
git pull origin develop
git branch -d feature/123-clipboard-sharing
git push origin --delete feature/123-clipboard-sharing
```

### Hotfix Workflow

```bash
# 1. Create hotfix branch from main
git checkout main
git pull origin main
git checkout -b hotfix/1.2.1-critical-security-fix

# 2. Fix the issue
git add .
git commit -m "fix: patch security vulnerability in auth"

# 3. Push to remote
git push origin hotfix/1.2.1-critical-security-fix

# 4. Create PR to main (for production)
# 5. After merge to main, also merge to develop
git checkout develop
git pull origin develop
git merge hotfix/1.2.1-critical-security-fix
git push origin develop

# 6. Tag the release
git checkout main
git tag -a v1.2.1 -m "Security hotfix release"
git push origin v1.2.1

# 7. Delete hotfix branch
git branch -d hotfix/1.2.1-critical-security-fix
git push origin --delete hotfix/1.2.1-critical-security-fix
```

### Release Workflow

```bash
# 1. Create release branch from develop
git checkout develop
git pull origin develop
git checkout -b release/1.1.0

# 2. Update version numbers, changelog, documentation
git add .
git commit -m "chore: prepare release 1.1.0"

# 3. Push to remote
git push origin release/1.1.0

# 4. Create PR to main
# 5. After merge to main, tag the release
git checkout main
git pull origin main
git tag -a v1.1.0 -m "Release version 1.1.0"
git push origin v1.1.0

# 6. Merge back to develop
git checkout develop
git merge release/1.1.0
git push origin develop

# 7. Delete release branch
git branch -d release/1.1.0
git push origin --delete release/1.1.0
```

## Commit Message Conventions

Follow [Conventional Commits](https://www.conventionalcommits.org/) specification:

### Format
```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, no logic change)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks, dependencies
- `perf`: Performance improvements
- `ci`: CI/CD changes

### Examples
```bash
feat(auth): add email verification for new users

Implements email verification flow with token-based validation.
Users must verify their email before accessing the dashboard.

Closes #123

---

fix(session): resolve timeout policy not being enforced

The session timeout was not properly checking activity timestamps.
Updated SessionManager to correctly validate session expiration.

Fixes #456

---

docs(readme): update Docker setup instructions

Added troubleshooting section for common Docker issues on macOS.
```

## Pull Request Guidelines

### PR Title Format
```
<type>(<scope>): <description> (#issue-number)
```

**Examples**:
- `feat(clipboard): add real-time notifications (#123)`
- `fix(auth): resolve CSRF token validation (#456)`
- `docs(api): update REST endpoint documentation (#789)`

### PR Description Template
```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Related Issues
Closes #123

## Testing
- [ ] Unit tests added/updated
- [ ] Integration tests pass
- [ ] Manual testing completed

## Checklist
- [ ] Code follows project style guidelines
- [ ] Self-review completed
- [ ] Documentation updated
- [ ] No new warnings generated
```

## Branch Protection Rules

### `main` Branch
- Require pull request reviews (minimum 2 approvals)
- Require status checks to pass
- Require branches to be up to date
- Require signed commits
- No force pushes
- No deletions

### `develop` Branch
- Require pull request reviews (minimum 1 approval)
- Require status checks to pass
- Require branches to be up to date
- No force pushes
- No deletions

## Version Numbering

Follow [Semantic Versioning](https://semver.org/) (SemVer):

### Format: `MAJOR.MINOR.PATCH`

- **MAJOR**: Incompatible API changes
- **MINOR**: Backward-compatible functionality additions
- **PATCH**: Backward-compatible bug fixes

**Examples**:
- `1.0.0` - Initial release
- `1.1.0` - New feature added
- `1.1.1` - Bug fix
- `2.0.0` - Breaking changes

### Pre-release Versions
- `1.0.0-alpha.1` - Alpha release
- `1.0.0-beta.1` - Beta release
- `1.0.0-rc.1` - Release candidate

## Best Practices

### Do's ✅
- Create branches from the correct base branch
- Keep branches focused on single features/fixes
- Commit frequently with clear messages
- Rebase feature branches regularly with develop
- Delete branches after merging
- Write descriptive PR descriptions
- Link PRs to related issues
- Keep branches up to date before merging

### Don'ts ❌
- Don't commit directly to `main` or `develop`
- Don't create long-lived feature branches
- Don't merge without code review
- Don't force push to shared branches
- Don't use generic commit messages ("fix", "update", "changes")
- Don't include unrelated changes in a single branch
- Don't leave stale branches undeleted

## Troubleshooting

### Resolving Merge Conflicts
```bash
# Update your branch with latest develop
git checkout feature/123-my-feature
git fetch origin
git rebase origin/develop

# If conflicts occur, resolve them
# Edit conflicted files, then:
git add <resolved-files>
git rebase --continue

# Push updated branch (may need force push after rebase)
git push origin feature/123-my-feature --force-with-lease
```

### Recovering from Mistakes
```bash
# Undo last commit (keep changes)
git reset --soft HEAD~1

# Undo last commit (discard changes)
git reset --hard HEAD~1

# Recover deleted branch (if not pushed)
git reflog
git checkout -b recovered-branch <commit-hash>
```

## References

- [Git Flow](https://nvie.com/posts/a-successful-git-branching-model/)
- [GitHub Flow](https://guides.github.com/introduction/flow/)
- [Conventional Commits](https://www.conventionalcommits.org/)
- [Semantic Versioning](https://semver.org/)

---

**Last Updated**: January 2026
**Maintained By**: Development Team