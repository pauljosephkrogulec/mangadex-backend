<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Chapter;
use App\Entity\CoverArt;
use App\Entity\Manga;
use App\Entity\MangaRecommendation;
use App\Entity\MangaRelation;
use App\Entity\Report;
use App\Entity\ScanlationGroup;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\CustomList;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class FakeDataGenerator
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function generateUser(): User
    {
        $user = new User();
        $user->setUsername($this->faker->userName);
        $user->setEmail($this->faker->safeEmail);
        $user->setPassword($this->faker->password(8, 20)); // Add password
        $user->setRoles(['ROLE_USER']);
        
        return $user;
    }

    public function generateAdminUser(): User
    {
        $user = new User();
        $user->setUsername($this->faker->userName . '_admin');
        $user->setEmail($this->faker->safeEmail);
        $user->setPassword($this->faker->password(8, 20)); // Add password
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        
        return $user;
    }

    public function generateTag(): Tag
    {
        $tagGroups = ['content', 'format', 'genre', 'theme'];
        $tagNames = [
            'Action', 'Adventure', 'Comedy', 'Drama', 'Fantasy', 'Horror',
            'Mystery', 'Romance', 'Sci-Fi', 'Slice of Life', 'Sports',
            'Supernatural', 'Thriller', 'Isekai', 'Mecha', 'Magic',
            'Demons', 'Game', 'Harem', 'Martial Arts', 'Military',
            'Psychological', 'School Life', 'Shoujo Ai', 'Shounen Ai',
            'Vampire', 'Yaoi', 'Yuri', 'Historical', 'Medical'
        ];

        $tag = new Tag();
        $tag->setName(['en' => $this->faker->randomElement($tagNames)]);
        $tag->setDescription(['en' => $this->faker->sentence(10)]);
        $tag->setTagGroup($this->faker->randomElement($tagGroups));
        
        return $tag;
    }

    public function generateAuthor(): Author
    {
        $author = new Author();
        $author->setName(['en' => $this->faker->name]);
        
        // Randomly add social media
        if ($this->faker->boolean(30)) {
            $author->setTwitter(['en' => '@' . $this->faker->userName]);
        }
        if ($this->faker->boolean(20)) {
            $author->setPixiv(['en' => $this->faker->userName]);
        }
        if ($this->faker->boolean(15)) {
            $author->setWebsite(['en' => $this->faker->url]);
        }
        
        return $author;
    }

    public function generateScanlationGroup(User $leader): ScanlationGroup
    {
        $group = new ScanlationGroup();
        $group->setName($this->faker->company . ' Scans');
        $group->setLeader($leader);
        $group->setDescription($this->faker->sentence(15));
        
        if ($this->faker->boolean(50)) {
            $group->setWebsite($this->faker->url);
        }
        if ($this->faker->boolean(40)) {
            $group->setDiscord($this->faker->word);
        }
        if ($this->faker->boolean(30)) {
            $group->setTwitter($this->faker->userName);
        }
        
        $group->setFocusedLanguages($this->faker->randomElements(['en', 'es', 'fr', 'de', 'pt', 'ru'], 2));
        $group->setVerified($this->faker->boolean(20));
        $group->setOfficial($this->faker->boolean(10));
        $group->setInactive($this->faker->boolean(15));
        
        return $group;
    }

    public function generateManga(array $authors, array $tags): Manga
    {
        $titles = [
            'The Legendary Hero', 'Dragon Chronicles', 'School Romance', 'Space Adventure',
            'Magic Academy', 'Ninja Tales', 'Robot Wars', 'Vampire Love', 'Time Travel',
            'Demon Slayer', 'Angel Story', 'Pirate Legend', 'Samurai Journey', 'Cyber Punk'
        ];

        $manga = new Manga();
        $manga->setTitle(['en' => $this->faker->randomElement($titles) . ' ' . $this->faker->randomNumber(2)]);
        $manga->setDescription(['en' => $this->faker->paragraph(3)]);
        $manga->setStatus($this->faker->randomElement(['ongoing', 'completed', 'hiatus', 'cancelled']));
        $manga->setContentRating($this->faker->randomElement(['safe', 'suggestive', 'erotica']));
        $manga->setOriginalLanguage($this->faker->randomElement(['ja', 'ko', 'zh', 'en']));
        $manga->setPublicationDemographic($this->faker->randomElement(['shounen', 'shoujo', 'seinen', 'josei']));
        $manga->setYear($this->faker->numberBetween(1990, 2023));
        $manga->setState('published');
        $manga->setAvailableTranslatedLanguages($this->faker->randomElements(['en', 'es', 'fr', 'de', 'pt'], rand(1, 3)));
        
        // Add random authors
        foreach ($this->faker->randomElements($authors, rand(1, 2)) as $author) {
            $manga->addAuthor($author);
        }
        
        // Add random artists (can be same as authors)
        foreach ($this->faker->randomElements($authors, rand(1, 2)) as $artist) {
            $manga->addArtist($artist);
        }
        
        // Add random tags
        foreach ($this->faker->randomElements($tags, rand(2, 5)) as $tag) {
            $manga->addTag($tag);
        }
        
        return $manga;
    }

    public function generateChapter(Manga $manga, User $uploader, array $scanlationGroups): Chapter
    {
        $chapter = new Chapter();
        $chapter->setTitle($this->faker->sentence(6));
        $chapter->setVolume((string)$this->faker->numberBetween(1, 10));
        $chapter->setChapter((string)$this->faker->numberBetween(1, 50));
        $chapter->setPages($this->faker->numberBetween(15, 45));
        $chapter->setTranslatedLanguage($this->faker->randomElement(['en', 'es', 'fr', 'de']));
        $chapter->setManga($manga);
        $chapter->setUploader($uploader);
        
        // Add to random scanlation group
        if (!empty($scanlationGroups)) {
            $chapter->addGroup($this->faker->randomElement($scanlationGroups));
        }
        
        // Random publish date in the past
        $publishDate = $this->faker->dateTimeBetween('-2 years', 'now');
        $chapter->setPublishAt(\DateTimeImmutable::createFromMutable($publishDate));
        
        return $chapter;
    }

    public function generateCoverArt(Manga $manga, User $uploader): CoverArt
    {
        $coverArt = new CoverArt();
        $coverArt->setFileName("covers/{$manga->getId()}/{$this->faker->uuid}.jpg");
        $coverArt->setVolume((string)$this->faker->numberBetween(1, 10));
        $coverArt->setDescription($this->faker->sentence(10));
        $coverArt->setLocale($this->faker->randomElement(['en', 'ja', null]));
        $coverArt->setManga($manga);
        $coverArt->setUploader($uploader);
        
        return $coverArt;
    }

    public function generateCustomList(User $owner, array $manga): CustomList
    {
        $listNames = [
            'My Favorites', 'To Read', 'Currently Reading', 'Completed', 'On Hold',
            'Dropped', 'Plan to Read', 'Best of 2023', 'Romantic Manga', 'Action Series'
        ];

        $list = new CustomList();
        $list->setName($this->faker->randomElement($listNames));
        $list->setOwner($owner);
        $list->setVisibility($this->faker->randomElement(['public', 'private']));
        
        // Add random manga to list
        foreach ($this->faker->randomElements($manga, rand(1, 5)) as $mangaItem) {
            $list->addManga($mangaItem);
        }
        
        return $list;
    }

    public function generateReport(User $creator, $object): Report
    {
        $reportReasons = [
            'Incorrect information', 'Inappropriate content', 'Spam', 'Copyright violation',
            'Poor quality translation', 'Missing chapters', 'Wrong categorization',
            'Duplicate entry', 'Broken links', 'Offensive language'
        ];

        $report = new Report();
        $report->setDetails($this->faker->randomElement($reportReasons) . ': ' . $this->faker->sentence(10));
        $report->setObjectId($object->getId());
        $report->setCreator($creator);
        $report->setStatus($this->faker->randomElement(['waiting', 'accepted', 'refused', 'autoresolved']));
        
        // Set the appropriate entity relationship
        if ($object instanceof Chapter) {
            $report->setChapter($object);
        } elseif ($object instanceof Author) {
            $report->setAuthor($object);
        } elseif ($object instanceof Manga) {
            $report->setManga($object);
        } elseif ($object instanceof Tag) {
            $report->setTag($object);
        } elseif ($object instanceof CoverArt) {
            $report->setCoverArt($object);
        } elseif ($object instanceof ScanlationGroup) {
            $report->setScanlationGroup($object);
        }
        
        return $report;
    }

    public function generateMangaRelation(Manga $manga, Manga $targetManga): MangaRelation
    {
        $relations = [
            'monochrome', 'main_story', 'adapted_from', 'based_on', 'prequel',
            'side_story', 'doujinshi', 'same_franchise', 'shared_universe',
            'sequel', 'spin_off', 'alternate_story', 'alternate_version',
            'preserialization', 'colored', 'serialization'
        ];

        $relation = new MangaRelation();
        $relation->setRelation($this->faker->randomElement($relations));
        $relation->setManga($manga);
        $relation->setTargetManga($targetManga);
        $relation->setSourceManga($manga);
        
        return $relation;
    }

    public function generateMangaRecommendation(Manga $manga, Manga $recommendedManga): MangaRecommendation
    {
        $recommendation = new MangaRecommendation();
        $recommendation->setScore($this->faker->randomFloat(2, 0.5, 1.0));
        $recommendation->setManga($manga);
        $recommendation->setRecommendedManga($recommendedManga);
        
        return $recommendation;
    }

    public function generateLargeDataset(ObjectManager $manager, int $scale = 100): void
    {
        // Generate base entities
        $users = [];
        for ($i = 0; $i < min($scale, 50); $i++) {
            $user = $this->generateUser();
            $manager->persist($user);
            $users[] = $user;
        }
        
        // Add admin users
        for ($i = 0; $i < 3; $i++) {
            $admin = $this->generateAdminUser();
            $manager->persist($admin);
            $users[] = $admin;
        }
        
        $tags = [];
        for ($i = 0; $i < min($scale / 2, 30); $i++) {
            $tag = $this->generateTag();
            $manager->persist($tag);
            $tags[] = $tag;
        }
        
        $authors = [];
        for ($i = 0; $i < min($scale / 2, 40); $i++) {
            $author = $this->generateAuthor();
            $manager->persist($author);
            $authors[] = $author;
        }
        
        $scanlationGroups = [];
        for ($i = 0; $i < min($scale / 3, 20); $i++) {
            $leader = $this->faker->randomElement($users);
            $group = $this->generateScanlationGroup($leader);
            $manager->persist($group);
            $scanlationGroups[] = $group;
        }
        
        // Generate manga
        $manga = [];
        for ($i = 0; $i < $scale; $i++) {
            $mangaEntity = $this->generateManga($authors, $tags);
            $manager->persist($mangaEntity);
            $manga[] = $mangaEntity;
        }
        
        // Generate chapters
        foreach ($manga as $mangaEntity) {
            $chapterCount = $this->faker->numberBetween(1, 20);
            for ($i = 0; $i < $chapterCount; $i++) {
                $uploader = $this->faker->randomElement($users);
                $chapter = $this->generateChapter($mangaEntity, $uploader, $scanlationGroups);
                $manager->persist($chapter);
            }
        }
        
        // Generate cover arts
        foreach ($manga as $mangaEntity) {
            $coverCount = $this->faker->numberBetween(1, 3);
            for ($i = 0; $i < $coverCount; $i++) {
                $uploader = $this->faker->randomElement($users);
                $coverArt = $this->generateCoverArt($mangaEntity, $uploader);
                $manager->persist($coverArt);
            }
        }
        
        // Generate custom lists
        foreach ($users as $user) {
            if ($this->faker->boolean(60)) {
                $listCount = $this->faker->numberBetween(1, 5);
                for ($i = 0; $i < $listCount; $i++) {
                    $list = $this->generateCustomList($user, $manga);
                    $manager->persist($list);
                }
            }
        }
        
        // Generate reports
        for ($i = 0; $i < min($scale / 2, 50); $i++) {
            $creator = $this->faker->randomElement($users);
            $object = $this->faker->randomElement(array_merge($manga, $authors, $tags));
            $report = $this->generateReport($creator, $object);
            $manager->persist($report);
        }
        
        // Generate manga relations
        for ($i = 0; $i < min($scale / 3, 30); $i++) {
            $manga1 = $this->faker->randomElement($manga);
            $manga2 = $this->faker->randomElement($manga);
            if ($manga1 !== $manga2) {
                $relation = $this->generateMangaRelation($manga1, $manga2);
                $manager->persist($relation);
            }
        }
        
        // Generate recommendations
        for ($i = 0; $i < min($scale, 100); $i++) {
            $manga1 = $this->faker->randomElement($manga);
            $manga2 = $this->faker->randomElement($manga);
            if ($manga1 !== $manga2) {
                $recommendation = $this->generateMangaRecommendation($manga1, $manga2);
                $manager->persist($recommendation);
            }
        }
    }
}
