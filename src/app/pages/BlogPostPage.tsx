import { useEffect } from "react";
import { useQuery } from "@tanstack/react-query";
import { format, parseISO } from "date-fns";
import { ArrowLeft, CalendarDays, Clock3, Link2, Send, Share2, Tag, UserRound } from "lucide-react";
import { Link, useParams } from "react-router";
import { Footer } from "../components/Footer";
import { Navbar } from "../components/Navbar";
import { getBlogPost } from "../blog/blog-api";
import { sanitizeRichHtml } from "../lib/sanitize-rich-html";
import "./BlogPage.css";

const FALLBACK_IMAGE =
  "https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=1400&q=80";

function formatDate(value: string | null) {
  if (!value) {
    return "Published";
  }

  const date = parseISO(value);

  return Number.isNaN(date.getTime()) ? "Published" : format(date, "MMMM dd, yyyy");
}

function RichArticleContent({ content }: { content: string }) {
  const normalized = content.trim();

  if (/<[a-z][\s\S]*>/i.test(normalized)) {
    return <div dangerouslySetInnerHTML={{ __html: sanitizeRichHtml(normalized) }} />;
  }

  return (
    <>
      {normalized
        .split(/\n{2,}/)
        .map((block) => block.trim())
        .filter(Boolean)
        .map((block, index) => {
          if (block.startsWith("## ")) {
            return <h2 key={index}>{block.replace(/^##\s+/, "")}</h2>;
          }

          if (block.startsWith("- ")) {
            return (
              <ul key={index}>
                {block.split(/\n/).map((item) => (
                  <li key={item}>{item.replace(/^-\s+/, "")}</li>
                ))}
              </ul>
            );
          }

          return <p key={index}>{block}</p>;
        })}
    </>
  );
}

export function BlogPostPage() {
  const { slug = "" } = useParams();
  const postQuery = useQuery({
    queryKey: ["blog-post", slug],
    queryFn: () => getBlogPost(slug),
    enabled: Boolean(slug),
    retry: false,
  });
  const post = postQuery.data;

  useEffect(() => {
    if (post?.meta_title) {
      document.title = post.meta_title;
    }
  }, [post?.meta_title]);

  return (
    <>
      <Navbar />
      <main className="blog-page">
        {postQuery.isLoading ? (
          <section className="blog-article-empty">
            <Clock3 aria-hidden="true" />
            <strong>Loading article</strong>
          </section>
        ) : postQuery.isError || !post ? (
          <section className="blog-article-empty">
            <Tag aria-hidden="true" />
            <strong>Article not found</strong>
            <Link to="/news">Back to News</Link>
          </section>
        ) : (
          <article className="blog-article">
            <Link className="blog-back-link" to="/news">
              <ArrowLeft aria-hidden="true" />
              News
            </Link>

            <header className="blog-article-hero blog-article-hero--detail">
              <div className="blog-card-kicker">
                <span>{post.category?.name ?? "Editorial"}</span>
                <span>{post.reading_minutes} min read</span>
              </div>
              <h1>{post.title}</h1>
              <p>{post.excerpt}</p>

              <div className="blog-article-toolbar">
                <div className="blog-article-author">
                  {post.author?.photo ? (
                    <img src={post.author.photo} alt="" aria-hidden="true" />
                  ) : (
                    <span>
                      <UserRound aria-hidden="true" />
                    </span>
                  )}
                  <div>
                    <strong>{post.author?.name ?? "Black Sky Editorial"}</strong>
                    <small>
                      <CalendarDays aria-hidden="true" />
                      {formatDate(post.published_at)}
                    </small>
                  </div>
                </div>

                <div className="blog-share-actions" aria-label="Share this news">
                  <a
                    href={`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}`}
                    target="_blank"
                    rel="noreferrer"
                    aria-label="Share to Facebook"
                  >
                    <Share2 aria-hidden="true" />
                  </a>
                  <a
                    href={`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(window.location.href)}`}
                    target="_blank"
                    rel="noreferrer"
                    aria-label="Share to LinkedIn"
                  >
                    <Send aria-hidden="true" />
                  </a>
                  <button
                    type="button"
                    aria-label="Copy article link"
                    onClick={() => void navigator.clipboard?.writeText(window.location.href)}
                  >
                    <Link2 aria-hidden="true" />
                  </button>
                </div>
              </div>
            </header>

            <img
              className="blog-article-image"
              src={post.featured_image ?? FALLBACK_IMAGE}
              alt={`${post.title} cover`}
            />

            <div className="blog-article-layout">
              <aside className="blog-article-aside">
                <strong>Tags</strong>
                <div>
                  {post.tags.map((tag) => (
                    <Link key={tag.slug} to={`/news?tag=${tag.slug}`}>
                      {tag.name}
                    </Link>
                  ))}
                </div>
              </aside>

              <div className="blog-article-content">
                <RichArticleContent content={post.content} />
              </div>
            </div>
          </article>
        )}
      </main>
      <Footer />
    </>
  );
}
