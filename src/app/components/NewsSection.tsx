import { useMemo, useRef, useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { format, parseISO } from "date-fns";
import { motion, useInView } from "motion/react";
import { ArrowRight, Clock, Tag } from "lucide-react";
import { Link } from "react-router";
import { getBlogPosts, type BlogPostSummary } from "../blog/blog-api";
import GradientText from "./GradientText";

const FALLBACK_IMAGE =
  "https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=1400&q=80";
const CATEGORY_COLORS = ["#A855F7", "#0EA5E9", "#E11D48", "#F97316", "#14B8A6", "#FFB700"];

interface Article {
  id: number;
  slug: string;
  title: string;
  excerpt: string;
  category: string;
  categoryColor: string;
  date: string;
  readTime: string;
  image: string;
}

function formatDate(value: string | null) {
  if (!value) {
    return "PUBLISHED";
  }

  const date = parseISO(value);

  return Number.isNaN(date.getTime()) ? "PUBLISHED" : format(date, "MMM d, yyyy").toUpperCase();
}

function toArticle(post: BlogPostSummary, index: number): Article {
  return {
    id: post.id,
    slug: post.slug,
    title: post.title,
    excerpt: post.excerpt ?? "Read the latest Black Sky Enterprise update.",
    category: (post.category?.name ?? "News").toUpperCase(),
    categoryColor: CATEGORY_COLORS[index % CATEGORY_COLORS.length],
    date: formatDate(post.published_at),
    readTime: `${post.reading_minutes} MIN READ`,
    image: post.featured_image ?? FALLBACK_IMAGE,
  };
}

function ArticleCard({
  article,
  index,
  variant = "compact",
}: {
  article: Article;
  index: number;
  variant?: "feature" | "wide" | "compact";
}) {
  const [hovered, setHovered] = useState(false);
  const isFeature = variant === "feature";
  const isWide = variant === "wide";
  const titleSize = isFeature
    ? "clamp(2rem, 3.3vw, 4rem)"
    : isWide
      ? "clamp(1.45rem, 2.1vw, 2.4rem)"
      : "clamp(1.2rem, 1.65vw, 1.75rem)";
  const excerptLines = isFeature ? 3 : 2;

  return (
    <motion.article
      initial={{ opacity: 0, y: 30 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true, margin: "-60px" }}
      transition={{ duration: 0.7, delay: index * 0.1, ease: [0.22, 1, 0.36, 1] }}
      onMouseEnter={() => setHovered(true)}
      onMouseLeave={() => setHovered(false)}
      className="relative h-full overflow-hidden cursor-pointer group"
      style={{
        background: "#0A0A0A",
        border: `1px solid ${hovered ? article.categoryColor + "40" : "rgba(255,255,255,0.06)"}`,
        transition: "border-color 0.4s",
      }}
    >
      <Link
        to={`/news/${article.slug}`}
        className="absolute inset-0 z-20"
        aria-label={`Read ${article.title}`}
      />
      <img
        src={article.image}
        alt={article.title}
        className="absolute inset-0 w-full h-full object-cover"
        loading={isFeature ? "eager" : "lazy"}
        style={{
          transform: hovered ? "scale(1.06)" : "scale(1)",
          transition: "transform 0.8s ease",
        }}
      />
      <div
        className="absolute inset-0"
        style={{
          background: isFeature
            ? "linear-gradient(90deg, rgba(5,5,5,0.88) 0%, rgba(5,5,5,0.38) 52%, rgba(5,5,5,0.78) 100%)"
            : "linear-gradient(180deg, rgba(5,5,5,0.18) 0%, rgba(5,5,5,0.58) 48%, rgba(5,5,5,0.96) 100%)",
        }}
      />
      <div
        className="absolute inset-0"
        style={{
          background: `radial-gradient(circle at 24% 84%, ${article.categoryColor}24 0%, transparent 62%)`,
          opacity: hovered ? 1 : 0.35,
          transition: "opacity 0.4s",
        }}
      />

      <div
        className="absolute top-5 left-5"
        style={{
          background: article.categoryColor,
          padding: "5px 13px",
        }}
      >
        <span
          style={{
            fontFamily: "'Barlow Condensed', sans-serif",
            fontWeight: 700,
            fontSize: "10px",
            letterSpacing: "0.3em",
            color: "#fff",
          }}
        >
          {article.category}
        </span>
      </div>

      <div
        className="absolute inset-x-0 bottom-0"
        style={{ padding: isFeature ? "clamp(1.4rem, 2.4vw, 2.25rem)" : "24px" }}
      >
        <h3
          style={{
            fontFamily: "'Barlow Condensed', sans-serif",
            fontWeight: 800,
            fontSize: titleSize,
            lineHeight: 0.98,
            color: hovered ? article.categoryColor : "#FFFFFF",
            marginBottom: "10px",
            transition: "color 0.3s",
            textTransform: "uppercase",
          }}
        >
          {article.title}
        </h3>

        <p
          style={{
            fontFamily: "'Barlow', sans-serif",
            fontWeight: 300,
            fontSize: "0.82rem",
            lineHeight: 1.7,
            color: "rgba(255,255,255,0.56)",
            marginBottom: "16px",
            maxWidth: isFeature ? "620px" : isWide ? "520px" : "100%",
            display: "-webkit-box",
            WebkitLineClamp: excerptLines,
            WebkitBoxOrient: "vertical",
            overflow: "hidden",
          }}
        >
          {article.excerpt}
        </p>

        <div
          className="flex items-center justify-between"
          style={{
            borderTop: "1px solid rgba(255,255,255,0.05)",
            paddingTop: "14px",
          }}
        >
          <div className="flex items-center gap-4">
            <div className="flex items-center gap-1.5">
              <Clock size={11} style={{ color: "rgba(255,255,255,0.25)" }} />
              <span
                style={{
                  fontFamily: "'Barlow Condensed', sans-serif",
                  fontWeight: 600,
                  fontSize: "10px",
                  letterSpacing: "0.2em",
                  color: "rgba(255,255,255,0.3)",
                }}
              >
                {article.date}
              </span>
            </div>
            <div className="flex items-center gap-1.5">
              <Tag size={11} style={{ color: "rgba(255,255,255,0.25)" }} />
              <span
                style={{
                  fontFamily: "'Barlow Condensed', sans-serif",
                  fontWeight: 600,
                  fontSize: "10px",
                  letterSpacing: "0.2em",
                  color: "rgba(255,255,255,0.3)",
                }}
              >
                {article.readTime}
              </span>
            </div>
          </div>

          <div
            className="flex items-center gap-2 transition-all duration-300"
            style={{
              color: hovered ? article.categoryColor : "rgba(255,255,255,0.25)",
            }}
          >
            <span
              style={{
                fontFamily: "'Barlow Condensed', sans-serif",
                fontWeight: 700,
                fontSize: "10px",
                letterSpacing: "0.25em",
              }}
            >
              READ
            </span>
            <ArrowRight
              size={12}
              style={{
                transform: hovered ? "translateX(4px)" : "translateX(0)",
                transition: "transform 0.3s",
              }}
            />
          </div>
        </div>
      </div>
    </motion.article>
  );
}

export function NewsSection() {
  const sectionRef = useRef<HTMLDivElement>(null);
  const isInView = useInView(sectionRef, { once: true, margin: "-100px" });
  const postsQuery = useQuery({
    queryKey: ["landing-news", "latest"],
    queryFn: () => getBlogPosts({ perPage: 4, sort: "latest" }),
  });
  const articles = useMemo(
    () => (postsQuery.data?.data ?? []).map(toArticle),
    [postsQuery.data?.data],
  );
  const featured = articles[0];
  const rest = articles.slice(1);

  return (
    <section
      id="news"
      ref={sectionRef}
      className="relative py-32 overflow-hidden"
      style={{ background: "#080808" }}
    >
      <div
        className="absolute"
        style={{
          width: "400px",
          height: "400px",
          bottom: "0",
          right: "10%",
          background: "radial-gradient(circle, rgba(225,29,72,0.06) 0%, transparent 70%)",
          pointerEvents: "none",
        }}
      />

      <div className="max-w-[1600px] mx-auto px-8 md:px-16">
        <div className="flex flex-col md:flex-row items-start md:items-end justify-between gap-6 mb-16">
          <div>
            <motion.div
              initial={{ opacity: 0, x: -20 }}
              animate={isInView ? { opacity: 1, x: 0 } : {}}
              transition={{ duration: 0.6 }}
              className="flex items-center gap-4 mb-4"
            >
              <div
                style={{
                  width: 32,
                  height: 2,
                  background: "linear-gradient(90deg, #E11D48, transparent)",
                }}
              />
              <span
                style={{
                  fontFamily: "'Barlow Condensed', sans-serif",
                  fontWeight: 600,
                  fontSize: "12px",
                  letterSpacing: "0.4em",
                  color: "#E11D48",
                }}
              >
                LATEST NEWS
              </span>
            </motion.div>

            <motion.h2
              initial={{ opacity: 0, y: 30 }}
              animate={isInView ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.8, delay: 0.1 }}
              style={{
                fontFamily: "'Barlow Condensed', sans-serif",
                fontWeight: 900,
                fontSize: "clamp(2.8rem, 6vw, 7rem)",
                lineHeight: 0.9,
                letterSpacing: "-0.01em",
                color: "#FFFFFF",
                textTransform: "uppercase",
              }}
            >
              NEWS &amp;
              <br />
              <GradientText
                colors={["#E11D48", "#F97316", "#E11D48", "#F97316", "#E11D48"]}
                animationSpeed={3}
                showBorder={false}
              >
                MEDIA
              </GradientText>
            </motion.h2>
          </div>

          <motion.a
            href="/news"
            initial={{ opacity: 0 }}
            animate={isInView ? { opacity: 1 } : {}}
            transition={{ delay: 0.3, duration: 0.6 }}
            className="flex items-center gap-3 px-6 py-3 transition-all duration-300"
            style={{
              border: "1px solid rgba(225,29,72,0.3)",
              fontFamily: "'Barlow Condensed', sans-serif",
              fontWeight: 700,
              fontSize: "11px",
              letterSpacing: "0.25em",
              color: "#E11D48",
              background: "rgba(225,29,72,0.05)",
              cursor: "pointer",
              flexShrink: 0,
              textDecoration: "none",
            }}
            whileHover={{ background: "rgba(225,29,72,0.1)" }}
          >
            ALL NEWS
            <ArrowRight size={12} />
          </motion.a>
        </div>

        {postsQuery.isLoading ? (
          <div className="min-h-[420px] grid place-items-center border border-white/10 bg-white/[0.02]">
            <span className="font-['Barlow_Condensed'] text-xs font-bold tracking-[0.35em] text-white/40">
              LOADING NEWS
            </span>
          </div>
        ) : postsQuery.isError || articles.length === 0 ? (
          <div className="min-h-[420px] grid place-items-center border border-white/10 bg-white/[0.02] px-8 text-center">
            <span className="font-['Barlow_Condensed'] text-xs font-bold tracking-[0.35em] text-white/40">
              NO NEWS PUBLISHED YET
            </span>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 md:auto-rows-[280px] xl:auto-rows-[300px] gap-4">
            {featured && (
              <div className="min-h-[460px] md:min-h-0 md:col-span-1 md:row-span-2 xl:col-span-2 xl:row-span-2">
                <ArticleCard article={featured} index={0} variant="feature" />
              </div>
            )}
            {rest[0] && (
              <div className="min-h-[320px] md:min-h-0 md:col-span-1 xl:col-span-2">
                <ArticleCard article={rest[0]} index={1} variant="wide" />
              </div>
            )}
            {rest[1] && (
              <div className="min-h-[300px] md:min-h-0 md:col-span-1 xl:col-span-1">
                <ArticleCard article={rest[1]} index={2} variant="compact" />
              </div>
            )}
            {rest[2] && (
              <div className="min-h-[300px] md:min-h-0 md:col-span-2 xl:col-span-1">
                <ArticleCard article={rest[2]} index={3} variant="compact" />
              </div>
            )}
          </div>
        )}
      </div>
    </section>
  );
}
