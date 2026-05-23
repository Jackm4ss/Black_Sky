import { useEffect, type CSSProperties } from "react";
import { useQuery } from "@tanstack/react-query";
import {
  ArrowLeft,
  ArrowUpRight,
  BriefcaseBusiness,
  CalendarDays,
  ImageIcon,
  MapPin,
  UsersRound,
} from "lucide-react";
import { Link, useParams } from "react-router";
import { Footer } from "../components/Footer";
import { Navbar } from "../components/Navbar";
import { getPortfolioWork } from "../portfolio/portfolio-api";
import "./PortfolioDetailPage.css";

const FALLBACK_IMAGE =
  "https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=1600&q=82";

function normalizeAccentColor(value?: string | null) {
  return value && /^#[0-9A-Fa-f]{6}$/.test(value) ? value : "#f97316";
}

function RichPortfolioContent({ content }: { content: string }) {
  const normalized = content.trim();

  if (/<[a-z][\s\S]*>/i.test(normalized)) {
    return <div dangerouslySetInnerHTML={{ __html: normalized }} />;
  }

  return (
    <>
      {normalized
        .split(/\n{2,}/)
        .map((block) => block.trim())
        .filter(Boolean)
        .map((block, index) => {
          if (block.startsWith("## ")) {
            return <h3 key={index}>{block.replace(/^##\s+/, "")}</h3>;
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

export function PortfolioDetailPage() {
  const { slug = "" } = useParams();
  const workQuery = useQuery({
    queryKey: ["portfolio-work", slug],
    queryFn: () => getPortfolioWork(slug),
    enabled: Boolean(slug),
    retry: false,
  });
  const work = workQuery.data;
  const heroImage = work?.featured_image ?? FALLBACK_IMAGE;
  const accentColor = normalizeAccentColor(work?.accent_color);
  const gallery = work ? [heroImage, ...work.gallery_images].filter(Boolean).slice(0, 6) : [];

  useEffect(() => {
    if (work?.meta_title) {
      document.title = work.meta_title;
    }
  }, [work?.meta_title]);

  return (
    <>
      <Navbar />
      <main className="portfolio-detail-page" style={{ "--portfolio-accent": accentColor } as CSSProperties}>
        {workQuery.isLoading ? (
          <section className="portfolio-detail-empty">
            <ImageIcon aria-hidden="true" />
            <strong>Loading project</strong>
          </section>
        ) : workQuery.isError || !work ? (
          <section className="portfolio-detail-empty">
            <BriefcaseBusiness aria-hidden="true" />
            <strong>Project not found</strong>
            <Link to="/#works">Back to Portfolio</Link>
          </section>
        ) : (
          <>
            <section className="portfolio-detail-hero">
              <img src={heroImage} alt="" aria-hidden="true" className="portfolio-detail-hero__bg" />
              <div className="portfolio-detail-hero__shade" />
              <div className="portfolio-detail-hero__inner">
                <Link className="portfolio-detail-back" to="/#works">
                  <ArrowLeft aria-hidden="true" />
                  Back to Portfolio
                </Link>
                <div className="portfolio-detail-kicker">
                  <span style={{ color: accentColor }}>{work.category}</span>
                  <span>{work.year}</span>
                </div>
                <h1>{work.title}</h1>
                <p>{work.excerpt}</p>
              </div>
            </section>

            <section className="portfolio-detail-body">
              <aside className="portfolio-detail-aside">
                <div className="portfolio-detail-summary-card">
                  <span>Project Details</span>
                  <dl>
                    <div>
                      <dt>
                        <CalendarDays aria-hidden="true" />
                        Year
                      </dt>
                      <dd>{work.year}</dd>
                    </div>
                    <div>
                      <dt>
                        <MapPin aria-hidden="true" />
                        Location
                      </dt>
                      <dd>{work.location}</dd>
                    </div>
                    {work.role && (
                      <div>
                        <dt>
                          <BriefcaseBusiness aria-hidden="true" />
                          Role
                        </dt>
                        <dd>{work.role}</dd>
                      </div>
                    )}
                    {work.attendance && (
                      <div>
                        <dt>
                          <UsersRound aria-hidden="true" />
                          Attendance
                        </dt>
                        <dd>{work.attendance}</dd>
                      </div>
                    )}
                  </dl>
                </div>
              </aside>

              <div className="portfolio-detail-main">
                <section className="portfolio-detail-section">
                  <div className="portfolio-detail-section__head">
                    <span style={{ color: accentColor }}>Overview</span>
                    <h2>About the Project</h2>
                  </div>
                  <div className="portfolio-detail-copy">
                    <RichPortfolioContent content={work.description} />
                  </div>
                </section>

                <section className="portfolio-detail-section">
                  <div className="portfolio-detail-section__head">
                    <span style={{ color: accentColor }}>Gallery</span>
                    <h2>Production Frames</h2>
                  </div>
                  <div className="portfolio-detail-gallery">
                    {gallery.map((image, index) => (
                      <img
                        key={`${image}-${index}`}
                        src={image}
                        alt={`${work.title} gallery ${index + 1}`}
                        loading={index < 2 ? "eager" : "lazy"}
                      />
                    ))}
                  </div>
                </section>

                <section className="portfolio-detail-cta">
                  <div>
                    <span>Black Sky Enterprise</span>
                    <h2>Built for live moments that scale.</h2>
                  </div>
                  <Link to="/#contact">
                    Start a Project
                    <ArrowUpRight aria-hidden="true" />
                  </Link>
                </section>
              </div>
            </section>
          </>
        )}
      </main>
      <Footer />
    </>
  );
}
