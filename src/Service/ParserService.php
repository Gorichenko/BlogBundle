<?php

namespace BlogBundle\Service;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DomCrawler\Crawler;
use Doctrine\ORM\EntityManager;
use App\Entity\Blog;
use Symfony\Component\Filesystem\Filesystem;

class ParserService
{
    protected $container;
    protected $em;
    protected $blog;

    public function __construct(
        Container $container,
        EntityManager $em,
        Blog $blog
    )
    {
        $this->container = $container;
        $this->em = $em;
        $this->blog = $blog;
    }

    public function getContent()
    {
        $articles_links = [];
        $data_site = file_get_contents('https://shkolazhizni.ru/articles/?page=1');
        $crawler = new Crawler($data_site);
        $links = $crawler->filter(".post_preview > h2 > a")->each(function (Crawler $node, $i) {

            if ($i % 2 != 0 ) {
                return $node->attr('href');
            }
        });

        foreach ($links as $key => $value) {
            if (!empty($value)) {
                $article_content = file_get_contents($value);
                $art_crawler = new Crawler($article_content );
                $final_content = [];
                $final_content['article_title'] = $art_crawler->filter("h1 > em")->text();
                $final_content['article_author'] = $art_crawler->filter("span[itemprop='name']")->text();
                $text = $art_crawler->filter("div[itemprop='articleBody'] > p")->each(function (Crawler $node, $i) {

                    if ($node->text() != null ) {
                        return $node->text();
                    }

                });

                $str_text = '';
                foreach ($text as $key => $val) {
                    $str_text .= $val . '/n';
                }
                $final_content['article_text'] = $str_text;
                $final_content['article_image'] = 'nature.jpeg';
                array_push($articles_links, $final_content);
            }
        }

        return $articles_links;
    }

    public function saveArticles()
    {
        try{
            $articles = $this->getContent();
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        try{
            if ($articles) {
                foreach ($articles as $item) {
                    $blog = new Blog();
                    $blog->setTitle($item['article_title']);
                    $blog->setAuthor($item['article_author']);
                    $blog->setBlog($item['article_text']);
                    $blog->setImage($item['article_image']);
                    $blog->setTags('symfony2, php, paradise, symblog');
                    $blog->setCreated(new \DateTime("2018-06-02 18:54:12"));
                    $blog->setUpdated(new \DateTime("2018-07-12 00:54:12"));
                    $this->em->persist($blog);
                    $this->em->flush();
                }
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

    public function test()
    {
        $fileSystem = new Filesystem();
        $fileSystem->dumpFile($this->container->get('kernel')->getRootDir().'/../var/logs/test.txt','content');
    }
}