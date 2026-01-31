<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Chapter;
use App\Entity\CoverArt;
use App\Entity\CustomList;
use App\Entity\Manga;
use App\Entity\MangaRecommendation;
use App\Entity\MangaRelation;
use App\Entity\Report;
use App\Entity\ScanlationGroup;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create base entities first
        $users = $this->createUsers($manager);
        $tags = $this->createTags($manager);
        $authors = $this->createAuthors($manager);
        $scanlationGroups = $this->createScanlationGroups($manager, $users);

        // Create manga and related entities
        $manga = $this->createManga($manager, $authors, $tags);
        $chapters = $this->createChapters($manager, $manga, $users, $scanlationGroups);
        $coverArts = $this->createCoverArts($manager, $manga, $users);
        $customLists = $this->createCustomLists($manager, $users, $manga);
        $reports = $this->createReports($manager, $users, $manga, $chapters, $authors);
        $mangaRelations = $this->createMangaRelations($manager, $manga);
        $recommendations = $this->createMangaRecommendations($manager, $manga);

        $manager->flush();
    }

    private function createUsers(ObjectManager $manager): array
    {
        $users = [];

        // Regular users
        $userData = [
            ['username' => 'testuser1', 'email' => 'test1@example.com', 'roles' => ['ROLE_USER']],
            ['username' => 'testuser2', 'email' => 'test2@example.com', 'roles' => ['ROLE_USER']],
            ['username' => 'manga_fan', 'email' => 'fan@example.com', 'roles' => ['ROLE_USER']],
            ['username' => 'translator', 'email' => 'translator@example.com', 'roles' => ['ROLE_USER']],
            ['username' => 'reader', 'email' => 'reader@example.com', 'roles' => ['ROLE_USER']],
        ];

        foreach ($userData as $data) {
            $user = new User();
            $user->setUsername($data['username']);
            $user->setEmail($data['email']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
            $user->setRoles($data['roles']);
            $manager->persist($user);
            $users[$data['username']] = $user;
        }

        // Admin user
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@test.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $manager->persist($admin);
        $users['admin'] = $admin;

        return $users;
    }

    private function createTags(ObjectManager $manager): array
    {
        $tags = [];

        $tagData = [
            ['name' => ['en' => 'Action'], 'description' => ['en' => 'High energy and conflict'], 'group' => 'genre'],
            ['name' => ['en' => 'Romance'], 'description' => ['en' => 'Love stories'], 'group' => 'genre'],
            ['name' => ['en' => 'Comedy'], 'description' => ['en' => 'Humorous content'], 'group' => 'genre'],
            ['name' => ['en' => 'Fantasy'], 'description' => ['en' => 'Magical elements'], 'group' => 'genre'],
            ['name' => ['en' => 'Isekai'], 'description' => ['en' => 'Transported to another world'], 'group' => 'theme'],
            ['name' => ['en' => 'Slice of Life'], 'description' => ['en' => 'Everyday experiences'], 'group' => 'theme'],
            ['name' => ['en' => 'Shounen'], 'description' => ['en' => 'Young male audience'], 'group' => 'demographic'],
            ['name' => ['en' => 'Full Color'], 'description' => ['en' => 'Colored artwork'], 'group' => 'format'],
        ];

        foreach ($tagData as $data) {
            $tag = new Tag();
            $tag->setName($data['name']);
            $tag->setDescription($data['description']);
            $tag->setTagGroup($data['group']);
            $manager->persist($tag);
            $tags[$data['name']['en']] = $tag;
        }

        return $tags;
    }

    private function createAuthors(ObjectManager $manager): array
    {
        $authors = [];

        $authorData = [
            ['name' => ['en' => 'Eiji Nakamura'], 'type' => 'author', 'twitter' => ['en' => '@eiji_nakamura']],
            ['name' => ['en' => 'Yuki Tanaka'], 'type' => 'artist', 'twitter' => ['en' => '@yuki_art']],
            ['name' => ['en' => 'Hiroshi Yamamoto'], 'type' => 'author', 'twitter' => ['en' => '@hiroshi_yama']],
            ['name' => ['en' => 'Sakura Watanabe'], 'type' => 'artist', 'twitter' => ['en' => '@sakura_w']],
            ['name' => ['en' => 'Kenji Sato'], 'type' => 'author', 'twitter' => ['en' => '@kenji_sato']],
        ];

        foreach ($authorData as $data) {
            $author = new Author();
            $author->setName($data['name']);
            if (isset($data['twitter'])) {
                $author->setTwitter($data['twitter']);
            }
            $manager->persist($author);
            $authors[$data['name']['en']] = $author;
        }

        return $authors;
    }

    private function createScanlationGroups(ObjectManager $manager, array $users): array
    {
        $groups = [];

        $groupData = [
            ['name' => 'Speed Scans', 'leader' => 'translator', 'description' => 'Fast translations'],
            ['name' => 'Quality Manga', 'leader' => 'testuser1', 'description' => 'High quality releases'],
            ['name' => 'Indie Translators', 'leader' => 'testuser2', 'description' => 'Independent group'],
        ];

        foreach ($groupData as $data) {
            $group = new ScanlationGroup();
            $group->setName($data['name']);
            $group->setLeader($users[$data['leader']]);
            $group->setDescription($data['description']);
            $group->setFocusedLanguages(['en', 'es']);
            $group->setVerified(true);
            $manager->persist($group);
            $groups[$data['name']] = $group;

            // Add some members
            foreach ($users as $user) {
                if ($user->getUsername() !== $data['leader'] && rand(0, 1)) {
                    $group->addMember($user);
                }
            }
        }

        return $groups;
    }

    private function createManga(ObjectManager $manager, array $authors, array $tags): array
    {
        $manga = [];

        $mangaData = [
            [
                'title' => ['en' => 'Dragon Quest Adventure'],
                'description' => ['en' => 'An epic fantasy adventure in a world of dragons and magic.'],
                'status' => 'ongoing',
                'contentRating' => 'safe',
                'originalLanguage' => 'ja',
                'publicationDemographic' => 'shounen',
                'year' => 2020,
                'authors' => ['Eiji Nakamura'],
                'artists' => ['Yuki Tanaka'],
                'tags' => ['Action', 'Fantasy', 'Shounen'],
            ],
            [
                'title' => ['en' => 'Love in Tokyo'],
                'description' => ['en' => 'A heartwarming romance set in modern Tokyo.'],
                'status' => 'completed',
                'contentRating' => 'safe',
                'originalLanguage' => 'ja',
                'publicationDemographic' => 'shoujo',
                'year' => 2019,
                'authors' => ['Hiroshi Yamamoto'],
                'artists' => ['Sakura Watanabe'],
                'tags' => ['Romance', 'Slice of Life'],
            ],
            [
                'title' => ['en' => 'Isekai Hero'],
                'description' => ['en' => 'A hero transported to another world to save humanity.'],
                'status' => 'ongoing',
                'contentRating' => 'suggestive',
                'originalLanguage' => 'ja',
                'publicationDemographic' => 'seinen',
                'year' => 2021,
                'authors' => ['Kenji Sato'],
                'artists' => ['Yuki Tanaka'],
                'tags' => ['Isekai', 'Action', 'Fantasy'],
            ],
        ];

        foreach ($mangaData as $data) {
            $mangaEntity = new Manga();
            $mangaEntity->setTitle($data['title']);
            $mangaEntity->setDescription($data['description']);
            $mangaEntity->setStatus($data['status']);
            $mangaEntity->setContentRating($data['contentRating']);
            $mangaEntity->setOriginalLanguage($data['originalLanguage']);
            $mangaEntity->setPublicationDemographic($data['publicationDemographic']);
            $mangaEntity->setYear($data['year']);
            $mangaEntity->setState('published');
            $mangaEntity->setAvailableTranslatedLanguages(['en']);

            // Add authors
            foreach ($data['authors'] as $authorName) {
                $mangaEntity->addAuthor($authors[$authorName]);
            }

            // Add artists
            foreach ($data['artists'] as $artistName) {
                $mangaEntity->addArtist($authors[$artistName]);
            }

            // Add tags
            foreach ($data['tags'] as $tagName) {
                $mangaEntity->addTag($tags[$tagName]);
            }

            $manager->persist($mangaEntity);
            $manga[$data['title']['en']] = $mangaEntity;
        }

        return $manga;
    }

    private function createChapters(ObjectManager $manager, array $manga, array $users, array $scanlationGroups): array
    {
        $chapters = [];

        foreach ($manga as $mangaTitle => $mangaEntity) {
            $chapterCount = 'completed' === $mangaEntity->getStatus() ? 5 : 3;

            for ($i = 1; $i <= $chapterCount; ++$i) {
                $chapter = new Chapter();
                $chapter->setTitle("Chapter {$i}: The Adventure Begins");
                $chapter->setVolume('1');
                $chapter->setChapter((string) $i);
                $chapter->setPages(rand(20, 40));
                $chapter->setTranslatedLanguage('en');
                $chapter->setManga($mangaEntity);
                $chapter->setUploader($users['translator']);

                // Add to scanlation group
                $group = array_values($scanlationGroups)[rand(0, count($scanlationGroups) - 1)];
                $chapter->addGroup($group);

                $manager->persist($chapter);
                $chapters[] = $chapter;
            }
        }

        return $chapters;
    }

    private function createCoverArts(ObjectManager $manager, array $manga, array $users): array
    {
        $coverArts = [];

        foreach ($manga as $mangaTitle => $mangaEntity) {
            $coverArt = new CoverArt();
            $coverArt->setFileName("covers/{$mangaEntity->getId()}/cover.jpg");
            $coverArt->setVolume('1');
            $coverArt->setDescription("Main cover for {$mangaTitle}");
            $coverArt->setManga($mangaEntity);
            $coverArt->setUploader($users['testuser1']);

            $manager->persist($coverArt);
            $coverArts[] = $coverArt;
        }

        return $coverArts;
    }

    private function createCustomLists(ObjectManager $manager, array $users, array $manga): array
    {
        $lists = [];

        $listData = [
            ['name' => 'My Favorites', 'owner' => 'testuser1', 'visibility' => 'public'],
            ['name' => 'To Read', 'owner' => 'testuser2', 'visibility' => 'private'],
            ['name' => 'Completed Series', 'owner' => 'manga_fan', 'visibility' => 'public'],
        ];

        foreach ($listData as $data) {
            $list = new CustomList();
            $list->setName($data['name']);
            $list->setOwner($users[$data['owner']]);
            $list->setVisibility($data['visibility']);

            // Add some manga to each list
            $mangaArray = array_values($manga);
            for ($i = 0; $i < min(2, count($mangaArray)); ++$i) {
                $list->addManga($mangaArray[$i]);
            }

            $manager->persist($list);
            $lists[] = $list;
        }

        return $lists;
    }

    private function createReports(ObjectManager $manager, array $users, array $manga, array $chapters, array $authors): array
    {
        $reports = [];

        // Create some sample reports
        $reportData = [
            ['details' => 'Incorrect chapter numbering', 'object' => $chapters[0], 'creator' => 'testuser1'],
            ['details' => 'Wrong author information', 'object' => array_values($authors)[0], 'creator' => 'testuser2'],
            ['details' => 'Inappropriate content', 'object' => array_values($manga)[0], 'creator' => 'manga_fan'],
        ];

        foreach ($reportData as $data) {
            $report = new Report();
            $report->setDetails($data['details']);
            $report->setObjectId($data['object']->getId());
            $report->setCreator($users[$data['creator']]);
            $report->setStatus('waiting');

            // Set the appropriate entity relationship
            if ($data['object'] instanceof Chapter) {
                $report->setChapter($data['object']);
            } elseif ($data['object'] instanceof Author) {
                $report->setAuthor($data['object']);
            } elseif ($data['object'] instanceof Manga) {
                $report->setManga($data['object']);
            }

            $manager->persist($report);
            $reports[] = $report;
        }

        return $reports;
    }

    private function createMangaRelations(ObjectManager $manager, array $manga): array
    {
        $relations = [];
        $mangaArray = array_values($manga);

        if (count($mangaArray) >= 2) {
            $relation = new MangaRelation();
            $relation->setRelation('sequel');
            $relation->setManga($mangaArray[0]);
            $relation->setTargetManga($mangaArray[1]);
            $relation->setSourceManga($mangaArray[0]);

            $manager->persist($relation);
            $relations[] = $relation;
        }

        return $relations;
    }

    private function createMangaRecommendations(ObjectManager $manager, array $manga): array
    {
        $recommendations = [];
        $mangaArray = array_values($manga);

        if (count($mangaArray) >= 2) {
            $recommendation = new MangaRecommendation();
            $recommendation->setScore(0.85);
            $recommendation->setManga($mangaArray[0]);
            $recommendation->setRecommendedManga($mangaArray[1]);

            $manager->persist($recommendation);
            $recommendations[] = $recommendation;
        }

        return $recommendations;
    }
}
