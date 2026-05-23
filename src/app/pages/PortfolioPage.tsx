import { useEffect, type CSSProperties } from "react";
import { useInfiniteQuery } from "@tanstack/react-query";
import { ArrowUpRight, CalendarDays, MapPin } from "lucide-react";
import { Link } from "react-router";
import { Footer } from "../components/Footer";
import { Navbar } from "../components/Navbar";
import {
  getPortfolioWorks,
  type PortfolioWorkSummary,
} from "../portfolio/portfolio-api";
import "./PortfolioPage.css";

const FALLBACK_IMAGE =
  "https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=1600&q=82";

function PortfolioProjectCard({ work, index }: { work: PortfolioWorkSummary; index: number }) {
  return (
    <article className="portfolio-page-card" style={{ "--project-accent": work.accent_color } as CSSProperties}>
      <Link to={`/portfolio/${work.slug}`} aria-label={`View ${work.title}`} className="portfolio-page-card__link" />
      <img
        src={work.featured_image ?? FALLBACK_IMAGE}
        alt={`${work.title} project`}
        className="portfolio-page-card__image"
        loading={index < 4 ? "eager" : "lazy"}
      />
      <div className="portfolio-page-card__shade" />
      <div className="portfolio-page-card__accent" aria-hidden="true" />

      <div className="portfolio-page-card__top">
        <ArrowUpRight aria-hidden="true" />
      </div>

      <div className="portfolio-page-card__content">
        <div>
          <h2>{work.title}</h2>
          <p>{work.excerpt}</p>
        </div>
        <dl>
          <div>
            <dt>
              <MapPin aria-hidden="true" />
              Location
            </dt>
            <dd>{work.location}</dd>
          </div>
          <div>
            <dt>
              <CalendarDays aria-hidden="true" />
              Year
            </dt>
            <dd>{work.year}</dd>
          </div>
        </dl>
      </div>
    </article>
  );
}

export function PortfolioPage() {
  const query = useInfiniteQuery({
    queryKey: ["portfolio-page"],
    queryFn: ({ pageParam }) =>
      getPortfolioWorks({
        cursor: pageParam,
        perPage: 8,
      }),
    initialPageParam: "",
    getNextPageParam: (lastPage) => lastPage.meta.next_cursor ?? undefined,
  });

  const pages = query.data?.pages ?? [];
  const works = pages.flatMap((page) => page.data);

  useEffect(() => {
    document.title = "Portfolio Projects | Black Sky Enterprise";

    const description =
      "Explore Black Sky Enterprise portfolio projects across concerts, festivals, production, and live entertainment campaigns.";
    const metaDescription = document.querySelector<HTMLMetaElement>("meta[name='description']");
    const canonical = document.querySelector<HTMLLinkElement>("link[rel='canonical']");

    metaDescription?.setAttribute("content", description);
    canonical?.setAttribute("href", `${window.location.origin}/portfolio`);
  }, []);

  return (
    <>
      <Navbar />
      <main className="portfolio-page">
        <section className="portfolio-page-hero">
          <div>
            <p>Our Portfolio</p>
            <h1>Project Archive</h1>
            <span>
              Concert productions, touring moments, launch campaigns, and live experiences shaped by Black Sky.
            </span>
          </div>
        </section>

        {query.isLoading ? (
          <section className="portfolio-page-state">
            <strong>Loading Projects</strong>
          </section>
        ) : query.isError ? (
          <section className="portfolio-page-state">
            <strong>Projects Could Not Load</strong>
            <span>Please refresh the page or try again shortly.</span>
          </section>
        ) : works.length === 0 ? (
          <section className="portfolio-page-state">
            <strong>No Portfolio Work Published Yet</strong>
          </section>
        ) : (
          <section className="portfolio-page-grid" aria-label="Portfolio projects">
            {works.map((work, index) => (
              <PortfolioProjectCard key={work.id} work={work} index={index} />
            ))}
          </section>
        )}

        {query.hasNextPage && (
          <div className="portfolio-page-load">
            <button
              type="button"
              onClick={() => query.fetchNextPage()}
              disabled={query.isFetchingNextPage}
            >
              {query.isFetchingNextPage ? "Loading" : "Load More Projects"}
            </button>
          </div>
        )}
      </main>
      <Footer />
    </>
  );
}
