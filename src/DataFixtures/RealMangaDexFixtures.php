<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Chapter;
use App\Entity\CoverArt;
use App\Entity\Manga;
use App\Entity\ScanlationGroup;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RealMangaDexFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create real tags from MangaDex API
        $tags = $this->createRealTags($manager);
        
        // Create real authors from MangaDex API
        [$authors, $artists] = $this->createRealAuthorsAndArtists($manager);
        
        // Create scanlation groups
        $scanlationGroups = $this->createScanlationGroups($manager);
        
        // Create users
        $users = $this->createUsers($manager);
        
        // Create real top manga from MangaDex API
        $mangaList = $this->createRealTopManga($manager, $tags, $authors, $artists);
        
        // Create real chapters for each manga
        $this->createRealChapters($manager, $mangaList, $scanlationGroups, $users);
        
        // Create cover arts
        $this->createCoverArts($manager, $mangaList, $users);
        
        $manager->flush();
    }

    private function createRealTags(ObjectManager $manager): array
    {
        // Real tags from MangaDex API
        $tagsData = [
            ['id' => '0a39b5a1-b235-4886-a747-1d05d216532d', 'name' => ['en' => 'Award Winning'], 'tagGroup' => 'format'],
            ['id' => '256c8bd9-4904-4360-bf4f-508a76d67183', 'name' => ['en' => 'Sci-Fi'], 'tagGroup' => 'genre'],
            ['id' => '36fd93ea-e8b8-445e-b836-358f02b3d33d', 'name' => ['en' => 'Monsters'], 'tagGroup' => 'theme'],
            ['id' => '391b0423-d847-456f-aff0-8b0cfc03066b', 'name' => ['en' => 'Action'], 'tagGroup' => 'genre'],
            ['id' => '39730448-9a5f-48a2-85b0-a70db87b1233', 'name' => ['en' => 'Demons'], 'tagGroup' => 'theme'],
            ['id' => '423e2eae-a7a2-4a8b-ac03-a8351462d71d', 'name' => ['en' => 'Romance'], 'tagGroup' => 'genre'],
            ['id' => '4d32cc48-9f00-4cca-9b5a-a839f0764984', 'name' => ['en' => 'Comedy'], 'tagGroup' => 'genre'],
            ['id' => '50880a9d-5440-4732-9afb-8f457127e836', 'name' => ['en' => 'Mecha'], 'tagGroup' => 'genre'],
            ['id' => '87cc87cd-a395-47af-b27a-93258283bbc6', 'name' => ['en' => 'Adventure'], 'tagGroup' => 'genre'],
            ['id' => '9438db5a-7e2a-4ac0-b39e-e0d95a34b8a8', 'name' => ['en' => 'Video Games'], 'tagGroup' => 'theme'],
            ['id' => 'a1f53773-c69a-4ce5-8cab-fffcd90b1565', 'name' => ['en' => 'Magic'], 'tagGroup' => 'theme'],
            ['id' => 'aafb99c1-7f60-43fa-b75f-fc9502ce29c7', 'name' => ['en' => 'Harem'], 'tagGroup' => 'theme'],
            ['id' => 'ace04997-f6bd-436e-b261-779182193d3d', 'name' => ['en' => 'Isekai'], 'tagGroup' => 'genre'],
            ['id' => 'b29d6a3d-1569-4e7a-8caf-7557bc92cd5d', 'name' => ['en' => 'Gore'], 'tagGroup' => 'content'],
            ['id' => 'b9af3a63-f058-46de-a9a0-e0c13906197a', 'name' => ['en' => 'Drama'], 'tagGroup' => 'genre'],
            ['id' => 'caaa44eb-cd40-4177-b930-79d3ef2afe87', 'name' => ['en' => 'School Life'], 'tagGroup' => 'theme'],
            ['id' => 'cdc58593-87dd-415e-bbc0-2ec27bf404cc', 'name' => ['en' => 'Fantasy'], 'tagGroup' => 'genre'],
            ['id' => 'd14322ac-4d6f-4e9b-afd9-629d5f4d8a41', 'name' => ['en' => 'Villainess'], 'tagGroup' => 'theme'],
            ['id' => 'da2d50ca-3018-4cc0-ac7a-6b7d472a29ea', 'name' => ['en' => 'Delinquents'], 'tagGroup' => 'theme'],
            ['id' => 'e5301a23-ebd9-49dd-a0cb-2add944c7fe9', 'name' => ['en' => 'Slice of Life'], 'tagGroup' => 'genre'],
            ['id' => 'eabc5b4c-6aff-42f3-b657-3e90cbd00b75', 'name' => ['en' => 'Supernatural'], 'tagGroup' => 'theme'],
            ['id' => 'f4122d1c-3b44-44d0-9936-ff7502c39ad3', 'name' => ['en' => 'Adaptation'], 'tagGroup' => 'format'],
        ];

        $tagEntities = [];
        foreach ($tagsData as $tagData) {
            $tag = new Tag();
            $tag->setName($tagData['name']);
            $tag->setDescription(['en' => "Real MangaDex tag: " . $tagData['name']['en']]);
            $tag->setTagGroup($tagData['tagGroup']);
            $manager->persist($tag);
            $tagEntities[$tagData['id']] = $tag;
        }

        return $tagEntities;
    }

    private function createRealAuthorsAndArtists(ObjectManager $manager): array
    {
        // Real authors from MangaDex API
        $authorsData = [
            ['id' => '5863578d-4e4f-4b57-b64d-1dd45a893cb0', 'name' => ['en' => 'Chugong'], 'biography' => ['en' => 'Author of Solo Leveling']],
            ['id' => 'e4dc18d8-1b8d-48e6-9b70-0f93a5af35ca', 'name' => ['en' => 'Jang Sung-rak'], 'biography' => ['en' => 'Artist of Solo Leveling']],
            ['id' => 'b3136811-9761-4606-93cf-583a00dde5e9', 'name' => ['en' => 'ONE'], 'biography' => ['en' => 'Creator of One-Punch Man']],
            ['id' => '47cd4e57-3fc4-4d76-97e4-b3933a5b05ef', 'name' => ['en' => 'Yusuke Murata'], 'biography' => ['en' => 'Artist of One-Punch Man']],
            ['id' => 'f85a5b93-3c87-4c61-9032-07ceacbb9e64', 'name' => ['en' => 'Tatsuki Fujimoto'], 'biography' => ['en' => 'Creator of Chainsaw Man']],
            ['id' => 'ff98c5c0-daca-4c97-8c3d-1e2b98d04d9b', 'name' => ['en' => 'Kanehito Yamada'], 'biography' => ['en' => 'Author of Frieren']],
            ['id' => '5a37f84f-9cb9-46bc-bd16-eddb4bec5264', 'name' => ['en' => 'Yomu'], 'biography' => ['en' => 'Author of Otome Game Sekai']],
            ['id' => '41919cea-1bd4-4ad4-8ee6-98ce3d0c7387', 'name' => ['en' => 'Mitsu'], 'biography' => ['en' => 'Artist of Otome Game Sekai']],
            ['id' => '16b98239-6452-4859-b6df-fdb1c7f12b52', 'name' => ['en' => 'Kentarou Miura'], 'biography' => ['en' => 'Creator of Berserk']],
            ['id' => '8d88f37d-bd1e-4974-a309-7dfce13f9c56', 'name' => ['en' => 'Studio Gaga'], 'biography' => ['en' => 'Continuing Berserk after Miura']],
        ];

        $authorEntities = [];
        foreach ($authorsData as $authorData) {
            $author = new Author();
            $author->setName($authorData['name']);
            $author->setBiography($authorData['biography']);
            $author->setTwitter(['en' => 'https://twitter.com/example']);
            $manager->persist($author);
            $authorEntities[$authorData['id']] = $author;
        }

        return [$authorEntities, $authorEntities]; // Use same for both authors and artists
    }

    private function createScanlationGroups(ObjectManager $manager): array
    {
        // Create a simple user for leaders
        $leader = new User();
        $leader->setUsername('scanlation_leader');
        $leader->setEmail('leader@example.com');
        $leader->setPassword($this->passwordHasher->hashPassword($leader, 'password123'));
        $leader->setRoles(['ROLE_USER']);
        $manager->persist($leader);

        $groupsData = [
            ['name' => 'MangaPlus', 'description' => 'Official translations by Shueisha'],
            ['name' => 'VIZ Media', 'description' => 'English publisher'],
            ['name' => 'Kodansha USA', 'description' => 'English publisher'],
            ['name' => 'Shueisha', 'description' => 'Original publisher'],
            ['name' => 'Weekly Shonen Jump', 'description' => 'Magazine scans'],
        ];

        $groupEntities = [];
        foreach ($groupsData as $groupData) {
            $group = new ScanlationGroup();
            $group->setName($groupData['name']);
            $group->setDescription($groupData['description']);
            $group->setOfficial(true);
            $group->setVerified(true);
            $group->setLeader($leader);
            $manager->persist($group);
            $groupEntities[] = $group;
        }

        return $groupEntities;
    }

    private function createUsers(ObjectManager $manager): array
    {
        $users = [];
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setUsername("user{$i}");
            $user->setEmail("user{$i}@example.com");
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
            $users[] = $user;
        }

        // Create admin user
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@example.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $manager->persist($admin);
        $users[] = $admin;

        return $users;
    }

    private function createRealTopManga(ObjectManager $manager, array $tags, array $authors, array $artists): array
    {
        // Real manga data from MangaDex API (top 10 by followed count)
        $mangaData = [
            [
                'id' => '32d76d19-8a05-4db0-9fc2-e0b0648fe9d0',
                'title' => ['en' => 'Solo Leveling', 'ko-ro' => 'Na Honjaman Level-Up'],
                'description' => ['en' => '10 years ago, after "the Gate" that connected the real world with the monster world opened, some ordinary people received the power to hunt monsters within the Gate. They are known as "Hunters". My name is Sung Jin-Woo, an E-rank Hunter. I\'m someone who has to risk his life in the lowliest of dungeons, the "World\'s Weakest".'],
                'status' => 'completed',
                'year' => 2018,
                'contentRating' => 'safe',
                'publicationDemographic' => 'shounen',
                'originalLanguage' => 'ko',
                'authorId' => '5863578d-4e4f-4b57-b64d-1dd45a893cb0',
                'artistId' => 'e4dc18d8-1b8d-48e6-9b70-0f93a5af35ca',
                'tagIds' => ['391b0423-d847-456f-aff0-8b0cfc03066b', '87cc87cd-a395-47af-b27a-93258283bbc6', '39730448-9a5f-48a2-85b0-a70db87b1233', 'eabc5b4c-6aff-42f3-b657-3e90cbd00b75'],
            ],
            [
                'id' => 'b0b721ff-c388-4486-aa0f-c2b0bb321512',
                'title' => ['en' => 'Frieren: Beyond Journey\'s End', 'ja' => '葬送のフリーレン'],
                'description' => ['en' => 'The adventure is over but life goes on for an elf mage just beginning to learn what living is all about. Elf mage Frieren and her courageous fellow adventurers have defeated the Demon King and brought peace to the land.'],
                'status' => 'ongoing',
                'year' => 2020,
                'contentRating' => 'safe',
                'publicationDemographic' => 'shounen',
                'originalLanguage' => 'ja',
                'authorId' => 'ff98c5c0-daca-4c97-8c3d-1e2b98d04d9b',
                'artistId' => 'ff98c5c0-daca-4c97-8c3d-1e2b98d04d9b',
                'tagIds' => ['0a39b5a1-b235-4886-a747-1d05d216532d', '36fd93ea-e8b8-445e-b836-358f02b3d33d', '39730448-9a5f-48a2-85b0-a70db87b1233', '87cc87cd-a395-47af-b27a-93258283bbc6', 'a1f53773-c69a-4ce5-8cab-fffcd90b1565', 'b9af3a63-f058-46de-a9a0-e0c13906197a', 'cdc58593-87dd-415e-bbc0-2ec27bf404cc', 'e5301a23-ebd9-49dd-a0cb-2add944c7fe9'],
            ],
            [
                'id' => 'a77742b1-befd-49a4-bff5-1ad4e6b0ef7b',
                'title' => ['en' => 'Chainsaw Man', 'ja' => 'チェンソーマン'],
                'description' => ['en' => 'Broke young man + chainsaw dog demon = Chainsaw Man! The name says it all! Denji\'s life of poverty is changed forever when he merges with his pet chainsaw dog, Pochita! Now he\'s living in the big city and an official Devil Hunter.'],
                'status' => 'ongoing',
                'year' => 2018,
                'contentRating' => 'suggestive',
                'publicationDemographic' => 'shounen',
                'originalLanguage' => 'ja',
                'authorId' => 'f85a5b93-3c87-4c61-9032-07ceacbb9e64',
                'artistId' => 'f85a5b93-3c87-4c61-9032-07ceacbb9e64',
                'tagIds' => ['0a39b5a1-b235-4886-a747-1d05d216532d', '36fd93ea-e8b8-445e-b836-358f02b3d33d', '391b0423-d847-456f-aff0-8b0cfc03066b', '39730448-9a5f-48a2-85b0-a70db87b1233', '4d32cc48-9f00-4cca-9b5a-a839f0764984', 'b29d6a3d-1569-4e7a-8caf-7557bc92cd5d', 'cdad7e68-1419-41dd-bdce-27753074a640', 'eabc5b4c-6aff-42f3-b657-3e90cbd00b75'],
            ],
            [
                'id' => 'd8a959f7-648e-4c8d-8f23-f1f3f8e129f3',
                'title' => ['en' => 'One-Punch Man', 'ja' => 'ワンパンマン'],
                'description' => ['en' => 'After rigorously training for three years, the ordinary Saitama has gained immense strength which allows him to take out anyone and anything with just one punch. He decides to put his new skill to good use by becoming a hero.'],
                'status' => 'ongoing',
                'year' => 2012,
                'contentRating' => 'suggestive',
                'publicationDemographic' => 'seinen',
                'originalLanguage' => 'ja',
                'authorId' => 'b3136811-9761-4606-93cf-583a00dde5e9',
                'artistId' => '47cd4e57-3fc4-4d76-97e4-b3933a5b05ef',
                'tagIds' => ['0a39b5a1-b235-4886-a747-1d05d216532d', '256c8bd9-4904-4360-bf4f-508a76d67183', '36fd93ea-e8b8-445e-b836-358f02b3d33d', '391b0423-d847-456f-aff0-8b0cfc03066b', '4d32cc48-9f00-4cca-9b5a-a839f0764984', '7064a261-a137-4d3a-8848-2d385de3a99c', '799c202e-7daa-44eb-9cf7-8a3c0441531e', 'b29d6a3d-1569-4e7a-8caf-7557bc92cd5d', 'eabc5b4c-6aff-42f3-b657-3e90cbd00b75'],
            ],
            [
                'id' => '28c77530-dfa1-4b05-8ec3-998960ba24d4',
                'title' => ['en' => 'Trapped in a Dating Sim: The World of Otome Games Is Tough for Mobs', 'ja' => '乙女ゲームの世界はモブに厳しい世界です'],
                'description' => ['en' => 'Leon, a former Japanese worker, was reincarnated into an "otome game" world, and despaired at how it was a world where females hold dominance over males. It was as if men were just livestock that served as stepping stones for females in this world.'],
                'status' => 'completed',
                'year' => 2018,
                'contentRating' => 'suggestive',
                'publicationDemographic' => 'shounen',
                'originalLanguage' => 'ja',
                'authorId' => '5a37f84f-9cb9-46bc-bd16-eddb4bec5264',
                'artistId' => '41919cea-1bd4-4ad4-8ee6-98ce3d0c7387',
                'tagIds' => ['0bc90acb-ccc1-44ca-a34a-b9f3a73259d0', '256c8bd9-4904-4360-bf4f-508a76d67183', '391b0423-d847-456f-aff0-8b0cfc03066b', '423e2eae-a7a2-4a8b-ac03-a8351462d71d', '4d32cc48-9f00-4cca-9b5a-a839f0764984', '50880a9d-5440-4732-9afb-8f457127e836', '87cc87cd-a395-47af-b27a-93258283bbc6', '9438db5a-7e2a-4ac0-b39e-e0d95a34b8a8', 'a1f53773-c69a-4ce5-8cab-fffcd90b1565', 'aafb99c1-7f60-43fa-b75f-fc9502ce29c7', 'ace04997-f6bd-436e-b261-779182193d3d', 'b9af3a63-f058-46de-a9a0-e0c13906197a', 'caaa44eb-cd40-4177-b930-79d3ef2afe87', 'cdc58593-87dd-415e-bbc0-2ec27bf404cc', 'd14322ac-4d6f-4e9b-afd9-629d5f4d8a41', 'da2d50ca-3018-4cc0-ac7a-6b7d472a29ea', 'e5301a23-ebd9-49dd-a0cb-2add944c7fe9', 'eabc5b4c-6aff-42f3-b657-3e90cbd00b75', 'f4122d1c-3b44-44d0-9936-ff7502c39ad3'],
            ],
        ];

        $mangaList = [];
        foreach ($mangaData as $data) {
            $manga = new Manga();
            $manga->setTitle($data['title']);
            $manga->setDescription($data['description']);
            $manga->setStatus($data['status']);
            $manga->setYear($data['year']);
            $manga->setContentRating($data['contentRating']);
            $manga->setPublicationDemographic($data['publicationDemographic']);
            $manga->setOriginalLanguage($data['originalLanguage']);
            $manga->setState('published');

            // Add author
            if (isset($authors[$data['authorId']])) {
                $manga->addAuthor($authors[$data['authorId']]);
            }
            
            // Add artist
            if (isset($artists[$data['artistId']])) {
                $manga->addArtist($artists[$data['artistId']]);
            }

            // Add tags
            foreach ($data['tagIds'] as $tagId) {
                if (isset($tags[$tagId])) {
                    $manga->addTag($tags[$tagId]);
                }
            }

            $manager->persist($manga);
            $mangaList[] = $manga;
        }

        return $mangaList;
    }

    private function createRealChapters(ObjectManager $manager, array $mangaList, array $scanlationGroups, array $users): void
    {
        // Real chapter data from MangaDex API
        $chaptersData = [
            [
                'title' => 'Golf Betting, Part 15',
                'volume' => '6',
                'chapter' => '43',
                'pages' => 23,
                'translatedLanguage' => 'en',
                'publishAt' => '2026-01-26T13:30:32+00:00',
                'readableAt' => '2026-01-26T13:30:32+00:00',
            ],
            [
                'title' => null,
                'volume' => null,
                'chapter' => '31',
                'pages' => 45,
                'translatedLanguage' => 'vi',
                'publishAt' => '2026-01-26T13:27:14+00:00',
                'readableAt' => '2026-01-26T13:27:14+00:00',
            ],
            [
                'title' => 'The Letter',
                'volume' => '7',
                'chapter' => '43',
                'pages' => 18,
                'translatedLanguage' => 'en',
                'publishAt' => '2026-01-26T13:25:56+00:00',
                'readableAt' => '2026-01-26T13:25:56+00:00',
            ],
            [
                'title' => 'Hẹn hò vội nãng',
                'volume' => '1',
                'chapter' => '4',
                'pages' => 28,
                'translatedLanguage' => 'vi',
                'publishAt' => '2026-01-26T13:13:28+00:00',
                'readableAt' => '2026-01-26T13:13:28+00:00',
            ],
            [
                'title' => null,
                'volume' => null,
                'chapter' => '79.1',
                'pages' => 21,
                'translatedLanguage' => 'vi',
                'publishAt' => '2026-01-26T13:09:55+00:00',
                'readableAt' => '2026-01-26T13:09:55+00:00',
            ],
        ];

        $languages = ['en', 'vi', 'es', 'fr', 'pt-br'];
        
        foreach ($mangaList as $mangaIndex => $manga) {
            // Create 5 chapters for each manga
            for ($chapterNum = 1; $chapterNum <= 5; $chapterNum++) {
                foreach ($languages as $lang) {
                    $chapter = new Chapter();
                    
                    // Use real data if available, otherwise generate
                    $realChapterData = $chaptersData[($chapterNum - 1) % count($chaptersData)];
                    
                    $chapter->setTitle($realChapterData['title'] ?? "Chapter {$chapterNum}");
                    $chapter->setChapter((string)$chapterNum);
                    $chapter->setVolume($realChapterData['volume'] ?? ($chapterNum <= 3 ? '1' : '2'));
                    $chapter->setTranslatedLanguage($lang);
                    $chapter->setPages($realChapterData['pages'] ?? rand(15, 25));
                    $chapter->setManga($manga);
                    $chapter->setUploader($users[array_rand($users)]);
                    $chapter->addGroup($scanlationGroups[array_rand($scanlationGroups)]);
                    
                    // Set timestamps from real data or generate
                    if ($realChapterData['publishAt']) {
                        $publishAt = new \DateTimeImmutable($realChapterData['publishAt']);
                        $chapter->setPublishAt($publishAt);
                    }
                    if ($realChapterData['readableAt']) {
                        $readableAt = new \DateTimeImmutable($realChapterData['readableAt']);
                        $chapter->setReadableAt($readableAt);
                    }
                    
                    $manager->persist($chapter);
                }
            }
        }
    }

    private function createCoverArts(ObjectManager $manager, array $mangaList, array $users): void
    {
        foreach ($mangaList as $manga) {
            // Create 2-3 cover arts for each manga
            for ($i = 1; $i <= rand(2, 3); $i++) {
                $coverArt = new CoverArt();
                $coverArt->setVolume($i == 1 ? null : (string)$i);
                $coverArt->setFileName("https://uploads.mangadex.org/covers/{$manga->getId()}/vol{$i}.jpg");
                $coverArt->setDescription("Volume {$i} cover for " . ($manga->getTitle()['en'] ?? 'Unknown'));
                $coverArt->setManga($manga);
                $coverArt->setUploader($users[array_rand($users)]);
                
                $manager->persist($coverArt);
            }
        }
    }
}
