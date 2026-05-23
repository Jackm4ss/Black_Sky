import { useMemo, useRef, useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { motion, useInView } from "motion/react";
import { ArrowRight, ArrowUpRight } from "lucide-react";
import { Link } from "react-router";
import {
  getPortfolioWorks,
  type PortfolioWorkSummary,
} from "../portfolio/portfolio-api";
import GradientText from "./GradientText";

const FALLBACK_IMAGE =
  "https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=1400&q=80";
const cardDescriptionColor = "rgba(235,245,255,0.78)";
const cardMetaColor = "rgba(226,238,255,0.68)";
const placement: Array<Work["type"]> = ["feature", "tall", "square", "square", "banner"];
const DEFAULT_ACCENT = "#F97316";

interface Work {
  id: number;
  slug: string;
  title: string;
  category: string;
  year: string;
  location: string;
  accentColor: string;
  image: string;
  description: string;
  type: "feature" | "tall" | "square" | "banner";
}

function normalizeAccentColor(value?: string | null) {
  return value && /^#[0-9A-Fa-f]{6}$/.test(value) ? value : DEFAULT_ACCENT;
}

function hexToRgba(hex: string, alpha: number) {
  const value = normalizeAccentColor(hex).replace("#", "");
  const r = parseInt(value.slice(0, 2), 16);
  const g = parseInt(value.slice(2, 4), 16);
  const b = parseInt(value.slice(4, 6), 16);

  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

function toWork(work: PortfolioWorkSummary, index: number): Work {
  return {
    id: work.id,
    slug: work.slug,
    title: work.title,
    category: work.category,
    year: work.year,
    location: work.location,
    accentColor: normalizeAccentColor(work.accent_color),
    image: work.featured_image ?? FALLBACK_IMAGE,
    description: work.excerpt,
    type: placement[index] ?? "square",
  };
}

function CardLink({ work }: { work: Work }) {
  return (
    <Link
      to={`/portfolio/${work.slug}`}
      className="absolute inset-0 z-20"
      aria-label={`View ${work.title}`}
    />
  );
}

function FeatureCard({ work, delay }: { work: Work; delay: number }) {
  const [hovered, setHovered] = useState(false);

  return (
    <motion.div
      initial={{ opacity: 0, y: 24 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true, margin: "-60px" }}
      transition={{ duration: 0.7, delay, ease: [0.22, 1, 0.36, 1] }}
      onMouseEnter={() => setHovered(true)}
      onMouseLeave={() => setHovered(false)}
      className="relative overflow-hidden cursor-pointer h-full"
      style={{
        border: `1px solid ${hovered ? hexToRgba(work.accentColor, 0.34) : "rgba(255,255,255,0.06)"}`,
        transition: "border-color 0.4s",
      }}
    >
      <CardLink work={work} />
      <img
        src={work.image}
        alt={work.title}
        className="w-full h-full object-cover"
        loading="lazy"
        style={{
          transform: hovered ? "scale(1.05)" : "scale(1)",
          transition: "transform 0.8s ease",
          position: "absolute",
          inset: 0,
        }}
      />
      <div
        className="absolute inset-0"
        style={{
          background:
            "linear-gradient(180deg, rgba(5,5,5,0.22) 0%, rgba(5,5,5,0.48) 48%, rgba(5,5,5,0.94) 100%), linear-gradient(135deg, rgba(5,5,5,0.8) 0%, rgba(5,5,5,0.18) 50%, rgba(5,5,5,0.62) 100%)",
        }}
      />
      <motion.div
        animate={{ opacity: hovered ? 0.95 : 0.4 }}
        className="absolute inset-0"
        style={{
          background: `radial-gradient(circle at 20% 80%, ${hexToRgba(work.accentColor, 0.22)} 0%, transparent 65%)`,
        }}
      />

      <div
        aria-hidden="true"
        className="absolute top-5 right-5 flex lg:hidden items-center justify-center"
        style={{ width: 40, height: 40, background: work.accentColor }}
      >
        <ArrowUpRight size={18} color="#fff" />
      </div>
      <motion.div
        animate={{ opacity: hovered ? 1 : 0, x: hovered ? 0 : 8, y: hovered ? 0 : -8 }}
        transition={{ duration: 0.25 }}
        className="absolute top-5 right-5 hidden lg:flex items-center justify-center"
        style={{ width: 40, height: 40, background: work.accentColor }}
      >
        <ArrowUpRight size={18} color="#fff" />
      </motion.div>

      <div className="absolute bottom-0 left-0 right-0 p-7">
        <div
          style={{
            width: hovered ? 48 : 28,
            height: 2,
            background: `linear-gradient(90deg, ${work.accentColor}, transparent)`,
            transition: "width 0.4s",
            marginBottom: "12px",
          }}
        />
        <h3
          style={{
            fontFamily: "'Barlow Condensed', sans-serif",
            fontWeight: 900,
            fontSize: "clamp(2rem, 3vw, 3.2rem)",
            lineHeight: 0.92,
            letterSpacing: "-0.01em",
            color: "#fff",
            textTransform: "uppercase",
            marginBottom: "8px",
          }}
        >
          {work.title}
        </h3>
        <p
          style={{
            fontFamily: "'Barlow', sans-serif",
            fontWeight: 400,
            fontSize: "0.82rem",
            color: cardDescriptionColor,
            marginBottom: "12px",
          }}
        >
          {work.description}
        </p>
        <div className="flex items-center justify-between">
          <span
            style={{
              fontFamily: "'Barlow Condensed', sans-serif",
              fontWeight: 700,
              fontSize: "11px",
              letterSpacing: "0.2em",
              color: cardMetaColor,
            }}
          >
            {work.location.toUpperCase()}
          </span>
          <span
            style={{
              fontFamily: "'Barlow Condensed', sans-serif",
              fontWeight: 700,
              fontSize: "11px",
              letterSpacing: "0.2em",
              color: work.accentColor,
            }}
          >
            {work.year}
          </span>
        </div>
      </div>
    </motion.div>
  );
}

function TallCard({ work, delay }: { work: Work; delay: number }) {
  const [hovered, setHovered] = useState(false);

  return (
    <motion.div
      initial={{ opacity: 0, x: 24 }}
      whileInView={{ opacity: 1, x: 0 }}
      viewport={{ once: true, margin: "-60px" }}
      transition={{ duration: 0.8, delay, ease: [0.22, 1, 0.36, 1] }}
      onMouseEnter={() => setHovered(true)}
      onMouseLeave={() => setHovered(false)}
      className="relative overflow-hidden cursor-pointer h-full"
      style={{
        border: `1px solid ${hovered ? hexToRgba(work.accentColor, 0.34) : "rgba(255,255,255,0.06)"}`,
        transition: "border-color 0.4s",
      }}
    >
      <CardLink work={work} />
      <img
        src={work.image}
        alt={work.title}
        className="w-full h-full object-cover"
        loading="lazy"
        style={{
          transform: hovered ? "scale(1.06)" : "scale(1)",
          transition: "transform 0.9s ease",
          position: "absolute",
          inset: 0,
        }}
      />
      <div
        className="absolute inset-0"
        style={{
          background:
            "linear-gradient(180deg, rgba(5,5,5,0.28) 0%, rgba(5,5,5,0.55) 45%, rgba(5,5,5,0.96) 100%)",
        }}
      />
      <motion.div
        animate={{ opacity: hovered ? 0.95 : 0.4 }}
        className="absolute inset-0"
        style={{
          background: `radial-gradient(circle at 50% 90%, ${hexToRgba(work.accentColor, 0.24)} 0%, transparent 65%)`,
        }}
      />

      <div
        aria-hidden="true"
        className="absolute top-5 right-5 flex lg:hidden items-center justify-center"
        style={{ width: 38, height: 38, background: work.accentColor }}
      >
        <ArrowUpRight size={16} color="#fff" />
      </div>
      <motion.div
        animate={{ opacity: hovered ? 1 : 0, x: hovered ? 0 : 8, y: hovered ? 0 : -8 }}
        transition={{ duration: 0.25 }}
        className="absolute top-5 right-5 hidden lg:flex items-center justify-center"
        style={{ width: 38, height: 38, background: work.accentColor }}
      >
        <ArrowUpRight size={16} color="#fff" />
      </motion.div>

      <div className="absolute bottom-0 left-0 right-0 p-6">
        <div
          style={{
            width: hovered ? 40 : 20,
            height: 2,
            background: work.accentColor,
            transition: "width 0.4s",
            marginBottom: "10px",
          }}
        />
        <h3
          style={{
            fontFamily: "'Barlow Condensed', sans-serif",
            fontWeight: 900,
            fontSize: "clamp(1.6rem, 2.2vw, 2.4rem)",
            lineHeight: 0.93,
            letterSpacing: "-0.01em",
            color: "#fff",
            textTransform: "uppercase",
            marginBottom: "8px",
          }}
        >
          {work.title}
        </h3>
        <p
          style={{
            fontFamily: "'Barlow', sans-serif",
            fontWeight: 400,
            fontSize: "0.78rem",
            color: cardDescriptionColor,
            lineHeight: 1.65,
            marginBottom: "12px",
          }}
        >
          {work.description}
        </p>
        <div className="flex items-center gap-2">
          <div style={{ width: 6, height: 6, borderRadius: "50%", background: work.accentColor }} />
          <span
            style={{
              fontFamily: "'Barlow Condensed', sans-serif",
              fontWeight: 700,
              fontSize: "10px",
              letterSpacing: "0.25em",
              color: cardMetaColor,
            }}
          >
            {work.location.toUpperCase()} - {work.year}
          </span>
        </div>
      </div>
    </motion.div>
  );
}

function SquareCard({ work, delay }: { work: Work; delay: number }) {
  const [hovered, setHovered] = useState(false);

  return (
    <motion.div
      initial={{ opacity: 0, y: 24 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true, margin: "-60px" }}
      transition={{ duration: 0.7, delay, ease: [0.22, 1, 0.36, 1] }}
      onMouseEnter={() => setHovered(true)}
      onMouseLeave={() => setHovered(false)}
      className="relative overflow-hidden cursor-pointer flex flex-col h-full"
      style={{
        background: "#0A0A0A",
        border: `1px solid ${hovered ? hexToRgba(work.accentColor, 0.31) : "rgba(255,255,255,0.06)"}`,
        transition: "border-color 0.4s",
      }}
    >
      <CardLink work={work} />
      <div className="relative overflow-hidden" style={{ flex: "0 0 58%" }}>
        <img
          src={work.image}
          alt={work.title}
          className="w-full h-full object-cover"
          loading="lazy"
          style={{
            transform: hovered ? "scale(1.07)" : "scale(1)",
            transition: "transform 0.8s ease",
          }}
        />
        <div
          className="absolute inset-0"
          style={{ background: "linear-gradient(180deg, transparent 40%, rgba(10,10,10,0.97) 100%)" }}
        />
        <motion.div
          animate={{ opacity: hovered ? 0.95 : 0.4 }}
          className="absolute inset-0"
          style={{
            background: `radial-gradient(circle at 50% 80%, ${hexToRgba(work.accentColor, 0.22)} 0%, transparent 65%)`,
          }}
        />

        <div
          aria-hidden="true"
          className="absolute top-4 right-4 flex lg:hidden items-center justify-center"
          style={{ width: 34, height: 34, background: work.accentColor }}
        >
          <ArrowUpRight size={14} color="#fff" />
        </div>
        <motion.div
          animate={{ opacity: hovered ? 1 : 0, x: hovered ? 0 : 8, y: hovered ? 0 : -8 }}
          transition={{ duration: 0.25 }}
          className="absolute top-4 right-4 hidden lg:flex items-center justify-center"
          style={{ width: 34, height: 34, background: work.accentColor }}
        >
          <ArrowUpRight size={14} color="#fff" />
        </motion.div>

      </div>

      <div className="flex-1 flex flex-col justify-between p-5">
        <div>
          <h3
            style={{
              fontFamily: "'Barlow Condensed', sans-serif",
              fontWeight: 900,
              fontSize: "clamp(1.3rem, 2vw, 1.9rem)",
              lineHeight: 0.94,
              letterSpacing: "-0.01em",
              color: "#fff",
              textTransform: "uppercase",
              marginBottom: "6px",
            }}
          >
            {work.title}
          </h3>
          <p
            style={{
              fontFamily: "'Barlow', sans-serif",
              fontWeight: 400,
              fontSize: "0.75rem",
              color: cardDescriptionColor,
              lineHeight: 1.65,
            }}
          >
            {work.description}
          </p>
        </div>
        <div
          className="flex items-center gap-2 mt-3 pt-3"
          style={{ borderTop: "1px solid rgba(255,255,255,0.05)" }}
        >
          <div style={{ width: 5, height: 5, borderRadius: "50%", background: work.accentColor }} />
          <span
            style={{
              fontFamily: "'Barlow Condensed', sans-serif",
              fontWeight: 700,
              fontSize: "10px",
              letterSpacing: "0.22em",
              color: cardMetaColor,
            }}
          >
            {work.location.toUpperCase()}
          </span>
          <span
            style={{
              marginLeft: "auto",
              fontFamily: "'Barlow Condensed', sans-serif",
              fontWeight: 700,
              fontSize: "11px",
              color: work.accentColor,
            }}
          >
            {work.year}
          </span>
        </div>
      </div>
    </motion.div>
  );
}

function BannerCard({ work, delay }: { work: Work; delay: number }) {
  const [hovered, setHovered] = useState(false);

  return (
    <motion.div
      initial={{ opacity: 0, y: 30 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true, margin: "-60px" }}
      transition={{ duration: 0.8, delay, ease: [0.22, 1, 0.36, 1] }}
      onMouseEnter={() => setHovered(true)}
      onMouseLeave={() => setHovered(false)}
      className="relative overflow-hidden cursor-pointer h-full"
      style={{
        border: `1px solid ${hovered ? hexToRgba(work.accentColor, 0.34) : "rgba(255,255,255,0.07)"}`,
        transition: "border-color 0.4s",
      }}
    >
      <CardLink work={work} />
      <img
        src={work.image}
        alt={work.title}
        className="w-full h-full object-cover"
        loading="lazy"
        style={{
          transform: hovered ? "scale(1.04)" : "scale(1)",
          transition: "transform 1s ease",
          position: "absolute",
          inset: 0,
        }}
      />
      <div
        className="absolute inset-0"
        style={{
          background:
            "linear-gradient(180deg, rgba(5,5,5,0.2) 0%, rgba(5,5,5,0.88) 100%), linear-gradient(90deg, rgba(5,5,5,0.96) 0%, rgba(5,5,5,0.58) 50%, rgba(5,5,5,0.78) 100%)",
        }}
      />
      <motion.div
        animate={{ opacity: hovered ? 0.95 : 0.38 }}
        className="absolute inset-0"
        style={{
          background: `radial-gradient(circle at 30% 50%, ${hexToRgba(work.accentColor, 0.2)} 0%, transparent 60%)`,
        }}
      />

      <div className="relative z-10 h-full flex items-center px-10 gap-12">
        <div className="flex-shrink-0">
          <div className="flex items-center gap-4 mb-3">
            <span
              style={{
                fontFamily: "'Barlow Condensed', sans-serif",
                fontWeight: 700,
                fontSize: "11px",
                letterSpacing: "0.2em",
                color: work.accentColor,
              }}
            >
              {work.year}
            </span>
          </div>
          <h3
            style={{
              fontFamily: "'Barlow Condensed', sans-serif",
              fontWeight: 900,
              fontSize: "clamp(2rem, 3.5vw, 4rem)",
              lineHeight: 0.9,
              letterSpacing: "-0.02em",
              color: "#fff",
              textTransform: "uppercase",
            }}
          >
            {work.title}
          </h3>
        </div>

        <div style={{ width: 1, height: 80, background: "rgba(255,255,255,0.1)", flexShrink: 0 }} />

        <div className="flex-1 max-w-sm">
          <p
            style={{
              fontFamily: "'Barlow', sans-serif",
              fontWeight: 400,
              fontSize: "0.9rem",
              color: cardDescriptionColor,
              lineHeight: 1.7,
            }}
          >
            {work.description}
          </p>
          <div className="flex items-center gap-2 mt-3">
            <div style={{ width: 5, height: 5, borderRadius: "50%", background: work.accentColor }} />
            <span
              style={{
                fontFamily: "'Barlow Condensed', sans-serif",
                fontWeight: 700,
                fontSize: "11px",
                letterSpacing: "0.25em",
                color: cardMetaColor,
              }}
            >
              {work.location.toUpperCase()}
            </span>
          </div>
        </div>

        <div className="flex-shrink-0 ml-auto">
          <motion.div
            animate={{
              background: hovered
                ? `linear-gradient(135deg, ${work.accentColor}, ${hexToRgba(work.accentColor, 0.73)})`
                : "transparent",
              borderColor: hovered ? work.accentColor : "rgba(255,255,255,0.15)",
            }}
            className="flex items-center gap-3 px-7 py-4"
            style={{
              border: "1px solid rgba(255,255,255,0.15)",
              fontFamily: "'Barlow Condensed', sans-serif",
              fontWeight: 700,
              fontSize: "11px",
              letterSpacing: "0.28em",
              color: "#fff",
            }}
          >
            VIEW PROJECT
            <ArrowRight
              size={13}
              style={{
                transform: hovered ? "translateX(4px)" : "translateX(0)",
                transition: "transform 0.3s",
              }}
            />
          </motion.div>
        </div>
      </div>
    </motion.div>
  );
}

function renderCard(work: Work, index: number) {
  const delay = 0.05 + index * 0.07;

  if (work.type === "square") {
    return <SquareCard work={work} delay={delay} />;
  }

  if (work.type === "tall") {
    return <TallCard work={work} delay={delay} />;
  }

  if (work.type === "banner") {
    return <BannerCard work={work} delay={delay} />;
  }

  return <FeatureCard work={work} delay={delay} />;
}

export function PortfolioSection() {
  const sectionRef = useRef<HTMLDivElement>(null);
  const isInView = useInView(sectionRef, { once: true, margin: "-100px" });
  const worksQuery = useQuery({
    queryKey: ["landing-portfolio"],
    queryFn: () => getPortfolioWorks({ perPage: 8 }),
  });
  const works = useMemo(
    () => (worksQuery.data?.data ?? []).map(toWork),
    [worksQuery.data?.data],
  );

  return (
    <section
      id="works"
      ref={sectionRef}
      className="relative py-32 overflow-hidden"
      style={{ background: "#050505" }}
    >
      <div
        className="absolute top-0 left-0 right-0"
        style={{
          height: "1px",
          background: "linear-gradient(90deg, transparent, rgba(249,115,22,0.35), transparent)",
        }}
      />

      <div className="max-w-[1600px] mx-auto px-8 md:px-16">
        <div className="flex flex-col xl:flex-row items-start xl:items-end justify-between gap-8 mb-14">
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
                  background: "linear-gradient(90deg, #F97316, transparent)",
                }}
              />
              <span
                style={{
                  fontFamily: "'Barlow Condensed', sans-serif",
                  fontWeight: 600,
                  fontSize: "12px",
                  letterSpacing: "0.4em",
                  color: "#F97316",
                }}
              >
                SELECTED WORKS
              </span>
            </motion.div>
            <motion.h2
              initial={{ opacity: 0, y: 30 }}
              animate={isInView ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.8, delay: 0.1, ease: [0.22, 1, 0.36, 1] }}
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
              OUR
              <br />
              <GradientText
                colors={["#F97316", "#FFB700", "#F97316", "#FFB700", "#F97316"]}
                animationSpeed={3}
                showBorder={false}
              >
                PORTFOLIO
              </GradientText>
            </motion.h2>
          </div>

          <div className="flex w-full xl:w-auto xl:items-end">
            <motion.div
              initial={{ opacity: 0 }}
              animate={isInView ? { opacity: 1 } : {}}
              transition={{ delay: 0.35, duration: 0.6 }}
            >
              <Link
                to="/portfolio"
                className="flex items-center gap-2 px-4 py-3 md:px-6 transition-all duration-300 whitespace-nowrap no-underline"
                style={{
                  border: "1px solid rgba(249,115,22,0.32)",
                  fontFamily: "'Barlow Condensed', sans-serif",
                  fontWeight: 700,
                  fontSize: "11px",
                  letterSpacing: "0.25em",
                  color: "#F97316",
                  background: "rgba(249,115,22,0.06)",
                  cursor: "pointer",
                  textDecoration: "none",
                  textTransform: "uppercase",
                }}
              >
                VIEW ALL PROJECTS
                <ArrowRight size={12} />
              </Link>
            </motion.div>
          </div>
        </div>

        {worksQuery.isLoading ? (
          <div className="min-h-[520px] grid place-items-center border border-white/10 bg-white/[0.02]">
            <span className="font-['Barlow_Condensed'] text-xs font-bold tracking-[0.35em] text-white/40">
              LOADING PORTFOLIO
            </span>
          </div>
        ) : worksQuery.isError || works.length === 0 ? (
          <div className="min-h-[520px] grid place-items-center border border-white/10 bg-white/[0.02] px-8 text-center">
            <span className="font-['Barlow_Condensed'] text-xs font-bold tracking-[0.35em] text-white/40">
              NO PORTFOLIO WORK PUBLISHED YET
            </span>
          </div>
        ) : (
          <>
            <div
              className="hidden xl:grid gap-3"
              style={{
                gridTemplateColumns: "1fr 1fr 1fr",
                gridTemplateRows: "360px 320px 220px",
              }}
            >
              {works[0] && (
                <div style={{ gridColumn: "1 / 3", gridRow: "1" }}>
                  <FeatureCard work={works[0]} delay={0.05} />
                </div>
              )}
              {works[1] && (
                <div style={{ gridColumn: "3", gridRow: "1 / 3" }}>
                  <TallCard work={works[1]} delay={0.12} />
                </div>
              )}
              {works[2] && (
                <div style={{ gridColumn: "1", gridRow: "2" }}>
                  <SquareCard work={works[2]} delay={0.18} />
                </div>
              )}
              {works[3] && (
                <div style={{ gridColumn: "2", gridRow: "2" }}>
                  <SquareCard work={works[3]} delay={0.24} />
                </div>
              )}
              {works[4] && (
                <div style={{ gridColumn: "1 / 4", gridRow: "3" }}>
                  <BannerCard work={works[4]} delay={0.3} />
                </div>
              )}
            </div>

            <div
              className="hidden md:grid xl:hidden gap-4"
              style={{
                gridTemplateColumns: "1fr 1fr",
                gridTemplateRows: "340px 300px 300px 240px",
              }}
            >
              {works[0] && (
                <div style={{ gridColumn: "1 / 3", gridRow: "1" }}>
                  <FeatureCard work={works[0]} delay={0.05} />
                </div>
              )}
              {works[1] && (
                <div style={{ gridColumn: "1", gridRow: "2 / 4" }}>
                  <TallCard work={works[1]} delay={0.12} />
                </div>
              )}
              {works[2] && (
                <div style={{ gridColumn: "2", gridRow: "2" }}>
                  <SquareCard work={works[2]} delay={0.18} />
                </div>
              )}
              {works[3] && (
                <div style={{ gridColumn: "2", gridRow: "3" }}>
                  <SquareCard work={works[3]} delay={0.24} />
                </div>
              )}
              {works[4] && (
                <div style={{ gridColumn: "1 / 3", gridRow: "4" }}>
                  <FeatureCard work={works[4]} delay={0.3} />
                </div>
              )}
            </div>

            <div className="flex flex-col gap-4 md:hidden">
              {works.slice(0, 5).map((work, index) => (
                <div key={work.id} style={{ height: work.type === "tall" ? "430px" : "360px" }}>
                  {renderCard(work, index)}
                </div>
              ))}
            </div>
          </>
        )}
      </div>
    </section>
  );
}
