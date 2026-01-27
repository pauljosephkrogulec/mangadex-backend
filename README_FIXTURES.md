# Test Data and Fixtures Documentation

This document explains how to use the test data fixtures and fake data generators for the MangaDex backend application.

## Overview

The application includes comprehensive test fixtures and data generators for all entities:

- **Users** - Regular users and admin accounts
- **Manga** - Main manga entities with metadata
- **Chapters** - Individual chapters with translations
- **Authors** - Authors and artists
- **Tags** - Categorization tags
- **Cover Arts** - Cover images
- **Custom Lists** - User-created manga lists
- **Scanlation Groups** - Translation groups
- **Reports** - Content moderation reports
- **Manga Relations** - Relationships between manga
- **Manga Recommendations** - Recommendation system data

## Available Fixtures

### 1. TestFixtures
**Location**: `src/DataFixtures/TestFixtures.php`

Creates a comprehensive set of test data with realistic relationships:
- 6 users (5 regular + 1 admin)
- 8 tags across different categories
- 5 authors (mix of authors and artists)
- 3 scanlation groups
- 3 manga with full metadata
- Multiple chapters per manga
- Cover arts, custom lists, reports, and relations

**Usage**:
```bash
php bin/console doctrine:fixtures:load --group=TestFixtures
```

### 2. LargeDatasetFixtures
**Location**: `src/DataFixtures/LargeDatasetFixtures.php`

Generates a large dataset for performance testing:
- 100 manga entities
- 50 users (including admins)
- 30 tags
- 40 authors
- 20 scanlation groups
- Related chapters, cover arts, lists, reports, etc.

**Usage**:
```bash
php bin/console doctrine:fixtures:load --group=LargeDatasetFixtures
```

### 3. RealMangaDexFixtures
**Location**: `src/DataFixtures/RealMangaDexFixtures.php`

Fetches real data from the MangaDex API and creates entities from it. Requires internet connection.

**Usage**:
```bash
php bin/console doctrine:fixtures:load --group=RealMangaDexFixtures
```

## Fake Data Generator

**Location**: `src/DataFixtures/FakeDataGenerator.php`

A utility class that provides methods to generate fake data for any entity:

```php
use App\DataFixtures\FakeDataGenerator;

$generator = new FakeDataGenerator();

// Generate individual entities
$user = $generator->generateUser();
$manga = $generator->generateManga($authors, $tags);
$chapter = $generator->generateChapter($manga, $user, $scanlationGroups);

// Generate large dataset
$generator->generateLargeDataset($entityManager, 100);
```

### Available Generator Methods

- `generateUser()` - Creates a regular user
- `generateAdminUser()` - Creates an admin user
- `generateTag()` - Creates a tag with random category
- `generateAuthor()` - Creates an author with optional social media
- `generateScanlationGroup(User $leader)` - Creates a scanlation group
- `generateManga(array $authors, array $tags)` - Creates manga with relationships
- `generateChapter(Manga $manga, User $uploader, array $scanlationGroups)` - Creates a chapter
- `generateCoverArt(Manga $manga, User $uploader)` - Creates cover art
- `generateCustomList(User $owner, array $manga)` - Creates a custom list
- `generateReport(User $creator, $object)` - Creates a report
- `generateMangaRelation(Manga $manga, Manga $targetManga)` - Creates a manga relation
- `generateMangaRecommendation(Manga $manga, Manga $recommendedManga)` - Creates a recommendation

## Running Tests

### Unit Tests
Test individual entity methods and properties:

```bash
# Run all unit tests
php bin/phpunit tests/Unit

# Run specific entity tests
php bin/phpunit tests/Unit/Entity/UserTest.php
php bin/phpunit tests/Unit/Entity/MangaTest.php
php bin/phpunit tests/Unit/Entity/ChapterTest.php
```

### Integration Tests
Test API endpoints and database interactions:

```bash
# Run all integration tests
php bin/phpunit tests/Integration

# Run API tests
php bin/phpunit tests/Integration/Api/MangaApiTest.php
php bin/phpunit tests/Integration/Api/UserApiTest.php

# Run database tests
php bin/phpunit tests/Integration/DataFixtures/DatabaseTest.php
```

### All Tests
```bash
php bin/phpunit
```

## Test Coverage

The test suite covers:

### Entity Tests
- All getter/setter methods
- Default values and constraints
- Collection management (add/remove)
- Relationship handling
- Timestamp management

### API Tests
- CRUD operations (Create, Read, Update, Delete)
- Authentication and authorization
- Search and filtering
- Pagination
- Validation errors
- Edge cases

### Database Tests
- Entity persistence
- Relationship integrity
- Data generation
- Timestamp behavior
- Large dataset handling

## Development Workflow

### 1. Setting up Test Environment
```bash
# Install dependencies
composer install --dev

# Create test database
php bin/console doctrine:database:create --env=test
php bin/console doctrine:schema:create --env=test

# Load test fixtures
php bin/console doctrine:fixtures:load --env=test --group=TestFixtures
```

### 2. Running Tests During Development
```bash
# Watch for changes and run tests automatically
php bin/phpunit --watch

# Run tests with coverage
php bin/phpunit --coverage-html coverage

# Run specific test groups
php bin/phpunit --group=unit
php bin/phpunit --group=integration
```

### 3. Adding New Tests

When adding new entities or features:

1. **Create unit tests** in `tests/Unit/Entity/`
2. **Create API tests** in `tests/Integration/Api/`
3. **Update fixtures** if needed
4. **Add generator methods** to `FakeDataGenerator.php`

Example structure for a new entity test:
```php
<?php

namespace App\Tests\Unit\Entity;

use App\Entity\NewEntity;
use PHPUnit\Framework\TestCase;

class NewEntityTest extends TestCase
{
    private NewEntity $entity;

    protected function setUp(): void
    {
        $this->entity = new NewEntity();
    }

    public function testEntityCreation(): void
    {
        $this->assertInstanceOf(NewEntity::class, $this->entity);
        // Add more tests...
    }
}
```

## Performance Considerations

### Large Dataset Testing
For performance testing with large datasets:

```bash
# Load large dataset (may take several minutes)
php bin/console doctrine:fixtures:load --group=LargeDatasetFixtures

# Run performance-focused tests
php bin/phpunit --group=performance
```

### Memory Management
The fake data generator includes memory management for large datasets:
- Batch processing for entity creation
- Periodic entity manager clearing
- Configurable dataset sizes

## Troubleshooting

### Common Issues

1. **Faker not found**: Ensure `fakerphp/faker` is installed
   ```bash
   composer require fakerphp/faker
   ```

2. **Database connection errors**: Check test database configuration
   ```bash
   php bin/console doctrine:database:create --env=test
   ```

3. **Fixture loading issues**: Clear cache and reload
   ```bash
   php bin/console cache:clear --env=test
   php bin/console doctrine:fixtures:load --env=test
   ```

4. **Test failures**: Check for missing dependencies or configuration issues

### Debug Mode
Run tests with verbose output for debugging:
```bash
php bin/phpunit --verbose --debug
```

## Best Practices

1. **Isolate tests**: Each test should be independent
2. **Use fixtures**: Leverage fixtures for consistent test data
3. **Mock external services**: Avoid real API calls in tests
4. **Clean up**: Properly clean up test data after each test
5. **Coverage**: Aim for high test coverage but focus on critical paths
6. **Performance**: Consider test execution time in CI/CD pipelines

## Contributing

When contributing new features:

1. Add corresponding tests
2. Update fixtures if new entities are added
3. Update this documentation
4. Ensure all tests pass before submitting

## CI/CD Integration

The test suite is designed to run in CI/CD environments:

```yaml
# Example GitHub Actions workflow
- name: Run Tests
  run: |
    composer install --prefer-dist --no-progress
    php bin/console doctrine:database:create --env=test
    php bin/console doctrine:schema:create --env=test
    php bin/phpunit
```

This comprehensive test suite ensures the reliability and quality of the MangaDex backend application.
