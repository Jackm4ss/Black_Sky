import { useEffect, useMemo, useRef, useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { motion, useInView, useMotionValue, animate } from "motion/react";
import { ArrowRight, MapPin, Calendar, ChevronLeft, ChevronRight } from "lucide-react";
import { getPublicEvents, type PublicEvent } from "../events/events-api";
import GradientText from "./GradientText";
import { SaveEventButton } from "./SaveEventButton";

const FALLBACK_EVENT_IMAGE =
  "https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=1200&q=80";

const CARD_WIDTH = 460;
const GAP = 20;
const DEFAULT_ACCENT = "#A855F7";

export interface EventSlide {
  id: number;
  title: string;
  slug: string;
  subtitle: string;
  date: string;
  year: string;
  venue: string;
  city: string;
  tag: string;
  accentColor: string;
  glowColor: string;
  image: string;
  vendorUrl: string | null;
  soldOut?: boolean;
}

function normalizeHex(value?: string | null) {
  return value && /^#[0-9A-Fa-f]{6}$/.test(value) ? value : DEFAULT_ACCENT;
}

function hexToRgba(hex: string, alpha: number) {
  const value = hex.replace("#", "");
  const r = parseInt(value.slice(0, 2), 16);
  const g = parseInt(value.slice(2, 4), 16);
  const b = parseInt(value.slice(4, 6), 16);

  return `rgba(${r},${g},${b},${alpha})`;
}

function glowFromAccent(accentColor: string) {
  return hexToRgba(accentColor, 0.45);
}

function getContrastText(hex: string) {
  const value = normalizeHex(hex).replace("#", "");
  const r = parseInt(value.slice(0, 2), 16) / 255;
  const g = parseInt(value.slice(2, 4), 16) / 255;
  const b = parseInt(value.slice(4, 6), 16) / 255;
  const srgb = [r, g, b].map((channel) =>
    channel <= 0.03928 ? channel / 12.92 : Math.pow((channel + 0.055) / 1.055, 2.4)
  );
  const luminance = 0.2126 * srgb[0] + 0.7152 * srgb[1] + 0.0722 * srgb[2];

  return luminance > 0.42 ? "#050505" : "#FFFFFF";
}

export function mapPublicEventToSlide(event: PublicEvent): EventSlide {
  const accentColor = normalizeHex(event.accent_color);

  return {
    id: event.id,
    title: event.title,
    slug: event.slug,
    subtitle: event.subtitle ?? "A Black Sky live entertainment experience.",
    date: event.date,
    year: event.start_date.slice(0, 4) || "2026",
    venue: event.venue,
    city: event.city,
    tag: event.genre,
    accentColor,
    glowColor: glowFromAccent(accentColor),
    image: event.image_url ?? FALLBACK_EVENT_IMAGE,
    vendorUrl: event.vendor_url,
    soldOut: event.status === "sold_out",
  };
}

function EventsSectionState({ title, body }: { title: string; body: string }) {
  return (
    <div
      className="relative flex min-h-[420px] w-full max-w-[720px] flex-col justify-end overflow-hidden"
      style={{
        background: "linear-gradient(135deg, rgba(168,85,247,0.16), rgba(14,165,233,0.1))",
        border: "1px solid rgba(255,255,255,0.08)",
        padding: "40px",
      }}
    >
      <div
        className="absolute inset-0"
        aria-hidden="true"
        style={{
          background:
            "radial-gradient(circle at 20% 20%, rgba(168,85,247,0.18), transparent 35%), radial-gradient(circle at 90% 80%, rgba(14,165,233,0.18), transparent 40%)",
        }}
      />
      <div className="relative">
        <div
          style={{
            width: 48,
            height: 2,
            background: "linear-gradient(90deg, #A855F7, transparent)",
            marginBottom: 22,
          }}
        />
        <h3
          style={{
            fontFamily: "'Barlow Condensed', sans-serif",
            fontWeight: 900,
            fontSize: "clamp(2rem, 4vw, 3rem)",
            lineHeight: 0.95,
            color: "#FFFFFF",
            textTransform: "uppercase",
            marginBottom: 12,
          }}
        >
          {title}
        </h3>
        <p
          style={{
            maxWidth: 440,
            fontFamily: "'Barlow', sans-serif",
            fontSize: "0.95rem",
            lineHeight: 1.7,
            color: "rgba(255,255,255,0.56)",
          }}
        >
          {body}
        </p>
      </div>
    </div>
  );
}

export function EventSlideCard({
  event,
  isActive,
  fluid = false,
}: {
  event: EventSlide;
  isActive: boolean;
  fluid?: boolean;
}) {
  const [hovered, setHovered] = useState(false);
  const ctaTextColor = getContrastText(event.accentColor);
  const detailHref = `/events/${encodeURIComponent(event.slug)}`;
  const ctaLabel = "VIEW DETAILS";
  const href = detailHref;

  return (
    <motion.div
      onMouseEnter={() => setHovered(true)}
      onMouseLeave={() => setHovered(false)}
      animate={{ scale: isActive ? 1 : 0.96, opacity: isActive ? 1 : 0.65 }}
      transition={{ duration: 0.4, ease: [0.22, 1, 0.36, 1] }}
      className="relative overflow-hidden cursor-grab active:cursor-grabbing flex-shrink-0"
      style={{
        width: fluid ? "100%" : CARD_WIDTH,
        background: "#0A0A0A",
        border: `1px solid ${isActive ? event.accentColor + "50" : "rgba(255,255,255,0.05)"}`,
        transition: "border-color 0.4s",
        userSelect: "none",
      }}
    >
      <div className="relative overflow-hidden" style={{ aspectRatio: "16/9" }}>
        <img
          src={event.image}
          alt={event.title}
          draggable={false}
          className="w-full h-full object-cover"
          style={{
            transform: hovered ? "scale(1.06)" : "scale(1)",
            transition: "transform 0.8s ease",
            pointerEvents: "none",
          }}
        />
        <div
          className="absolute inset-0"
          style={{
            background: "linear-gradient(180deg, rgba(5,5,5,0.1) 0%, rgba(10,10,10,0.92) 100%)",
          }}
        />
        <motion.div
          className="absolute inset-0"
          animate={{ opacity: isActive ? 1 : 0 }}
          transition={{ duration: 0.5 }}
          style={{
            background: `radial-gradient(ellipse at 50% 110%, ${event.glowColor} 0%, transparent 65%)`,
          }}
        />

        <div
          className="absolute top-5 left-5 flex items-center gap-2"
          style={{
            background: "rgba(5,5,5,0.82)",
            backdropFilter: "blur(10px)",
            padding: "5px 14px",
            border: `1px solid ${event.accentColor}35`,
          }}
        >
          <div
            style={{
              width: 6,
              height: 6,
              borderRadius: "50%",
              background: event.accentColor,
              boxShadow: `0 0 6px ${event.accentColor}`,
            }}
          />
          <span
            style={{
              fontFamily: "'Barlow Condensed', sans-serif",
              fontWeight: 700,
              fontSize: "10px",
              letterSpacing: "0.32em",
              color: event.accentColor,
            }}
          >
            {event.tag}
          </span>
        </div>

        {event.soldOut && (
          <div
            className="absolute top-5 right-5 px-3 py-1"
            style={{ background: "#E11D48" }}
          >
            <span
              style={{
                fontFamily: "'Barlow Condensed', sans-serif",
                fontWeight: 700,
                fontSize: "9px",
                letterSpacing: "0.35em",
                color: "#fff",
              }}
            >
              SOLD OUT
            </span>
          </div>
        )}
      </div>

      <div className="p-7">
        <div
          className="mb-5"
          style={{
            width: isActive ? 48 : 24,
            height: 2,
            background: `linear-gradient(90deg, ${event.accentColor}, transparent)`,
            transition: "width 0.4s ease",
          }}
        />

        <h3
          style={{
            fontFamily: "'Barlow Condensed', sans-serif",
            fontWeight: 900,
            fontSize: "clamp(2rem, 3.5vw, 2.8rem)",
            lineHeight: 0.92,
            letterSpacing: "-0.01em",
            color: "#FFFFFF",
            textTransform: "uppercase",
            marginBottom: "10px",
          }}
        >
          {event.title}
        </h3>
        <p
          style={{
            fontFamily: "'Barlow', sans-serif",
            fontWeight: 300,
            fontSize: "0.82rem",
            color: "rgba(255,255,255,0.4)",
            lineHeight: 1.6,
            marginBottom: "20px",
          }}
        >
          {event.subtitle}
        </p>

        <div className="flex flex-col gap-2.5 mb-6">
          <div className="flex items-center gap-2.5">
            <Calendar size={12} style={{ color: event.accentColor, flexShrink: 0 }} />
            <span
              style={{
                fontFamily: "'Barlow Condensed', sans-serif",
                fontWeight: 600,
                fontSize: "11px",
                letterSpacing: "0.22em",
                color: "rgba(255,255,255,0.55)",
              }}
            >
              {event.date}
            </span>
          </div>
          <div className="flex items-center gap-2.5">
            <MapPin size={12} style={{ color: event.accentColor, flexShrink: 0 }} />
            <span
              style={{
                fontFamily: "'Barlow Condensed', sans-serif",
                fontWeight: 600,
                fontSize: "11px",
                letterSpacing: "0.22em",
                color: "rgba(255,255,255,0.55)",
              }}
            >
              {event.venue}, {event.city}
            </span>
          </div>
        </div>

        <div
          className="flex items-center justify-between gap-3 pt-5"
          style={{ borderTop: "1px solid rgba(255,255,255,0.07)" }}
        >
          <SaveEventButton
            eventId={event.id}
            compact
            idleLabel="Save"
            savedLabel="Saved"
            loginLabel="Save"
            className="event-slide-save-button"
          />
          <motion.a
            href={href}
            whileHover={{
              backgroundColor: event.accentColor,
              color: ctaTextColor,
              boxShadow: `0 0 22px ${event.glowColor}`,
            }}
            whileTap={{
              scale: 0.97,
              backgroundColor: event.accentColor,
              color: ctaTextColor,
              boxShadow: `0 0 28px ${event.glowColor}`,
            }}
            transition={{ duration: 0.18 }}
            style={{
              display: "flex",
              alignItems: "center",
              justifyContent: "center",
              gap: "8px",
              fontFamily: "'Barlow Condensed', sans-serif",
              fontWeight: 700,
              fontSize: "11px",
              letterSpacing: "0.28em",
              color: "#FFFFFF",
              background: "transparent",
              border: `1px solid ${event.soldOut ? event.accentColor + "80" : event.accentColor}`,
              cursor: "pointer",
              minHeight: 42,
              padding: "0 18px",
              textDecoration: "none",
              boxShadow: `inset 0 0 0 1px rgba(255,255,255,0.04)`,
            }}
          >
            {ctaLabel}
            <ArrowRight
              size={12}
              style={{
                transform: hovered ? "translateX(5px)" : "translateX(0)",
                transition: "transform 0.3s",
              }}
            />
          </motion.a>
          {/* Reserved year marker for future event-card metadata. */}
          {/* <span
            style={{
              fontFamily: "'Barlow Condensed', sans-serif",
              fontWeight: 700,
              fontSize: "11px",
              letterSpacing: "0.3em",
              color: "rgba(255,255,255,0.72)",
            }}
          >
            BSE - {event.year}
          </span> */}
        </div>
      </div>
    </motion.div>
  );
}

export function EventsSection() {
  const sectionRef = useRef<HTMLDivElement>(null);
  const isInView = useInView(sectionRef, { once: true, margin: "-100px" });
  const [activeIndex, setActiveIndex] = useState(0);
  const x = useMotionValue(0);
  const { data, isError, isLoading } = useQuery({
    queryKey: ["landing-events", "upcoming", "latest"],
    queryFn: () => getPublicEvents({ timeframe: "upcoming", availability: "all", sort: "latest", perPage: 8 }),
    staleTime: 60_000,
  });

  const events = useMemo(() => (data?.data ?? []).map(mapPublicEventToSlide), [data]);
  const TOTAL = events.length;

  useEffect(() => {
    setActiveIndex(0);
    x.set(0);
  }, [TOTAL, x]);

  const goTo = (index: number) => {
    if (TOTAL === 0) {
      return;
    }

    const clamped = Math.max(0, Math.min(index, TOTAL - 1));
    setActiveIndex(clamped);
    animate(x, -(clamped * (CARD_WIDTH + GAP)), {
      type: "spring",
      stiffness: 280,
      damping: 28,
      mass: 0.9,
    });
  };

  const handleDragEnd = (_: unknown, info: { offset: { x: number }; velocity: { x: number } }) => {
    if (TOTAL <= 1) {
      goTo(0);
      return;
    }

    const swipeThreshold = CARD_WIDTH * 0.25;
    const velThreshold = 500;
    if (info.offset.x < -swipeThreshold || info.velocity.x < -velThreshold) {
      goTo(activeIndex + 1);
    } else if (info.offset.x > swipeThreshold || info.velocity.x > velThreshold) {
      goTo(activeIndex - 1);
    } else {
      goTo(activeIndex);
    }
  };

  const maxDrag = TOTAL > 1 ? -((TOTAL - 1) * (CARD_WIDTH + GAP)) : 0;
  const progressPct = TOTAL > 1 ? (activeIndex / (TOTAL - 1)) * 100 : TOTAL === 1 ? 100 : 0;
  const currentCount = TOTAL > 0 ? activeIndex + 1 : 0;

  return (
    <section
      id="events"
      ref={sectionRef}
      className="relative py-32 overflow-hidden"
      style={{ background: "#080808" }}
    >
      <div
        className="absolute pointer-events-none"
        style={{
          width: "700px",
          height: "700px",
          top: "40%",
          left: "50%",
          transform: "translate(-50%,-50%)",
          background: "radial-gradient(circle, rgba(168,85,247,0.05) 0%, transparent 70%)",
        }}
      />

      <div className="max-w-[1600px] mx-auto">
        <div className="px-8 md:px-16 flex flex-col md:flex-row items-start md:items-end justify-between gap-6 mb-12">
          <div>
            <motion.div
              initial={{ opacity: 0, x: -20 }}
              animate={isInView ? { opacity: 1, x: 0 } : {}}
              transition={{ duration: 0.6 }}
              className="flex items-center gap-4 mb-4"
            >
              <div style={{ width: 32, height: 2, background: "linear-gradient(90deg, #A855F7, transparent)" }} />
              <span style={{ fontFamily: "'Barlow Condensed', sans-serif", fontWeight: 600, fontSize: "12px", letterSpacing: "0.4em", color: "#A855F7" }}>
                UPCOMING
              </span>
            </motion.div>
            <motion.h2
              initial={{ opacity: 0, y: 30 }}
              animate={isInView ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.8, delay: 0.1, ease: [0.22, 1, 0.36, 1] }}
              style={{ fontFamily: "'Barlow Condensed', sans-serif", fontWeight: 900, fontSize: "clamp(2.8rem, 6vw, 7rem)", lineHeight: 0.9, letterSpacing: "-0.01em", color: "#FFFFFF", textTransform: "uppercase" }}
            >
              EVENTS &amp;
              <br />
              <GradientText
                colors={["#A855F7", "#0EA5E9", "#A855F7", "#0EA5E9", "#A855F7"]}
                animationSpeed={3}
                showBorder={false}
              >
                SHOWS
              </GradientText>
            </motion.h2>
          </div>

          <motion.div
            initial={{ opacity: 0 }}
            animate={isInView ? { opacity: 1 } : {}}
            transition={{ delay: 0.35, duration: 0.6 }}
            className="flex flex-wrap items-center gap-3 md:gap-4 flex-shrink-0"
          >
            <div className="flex items-baseline gap-1">
              <span style={{ fontFamily: "'Barlow Condensed', sans-serif", fontWeight: 900, fontSize: "2rem", color: "#fff", lineHeight: 1 }}>
                {String(currentCount).padStart(2, "0")}
              </span>
              <span style={{ fontFamily: "'Barlow Condensed', sans-serif", fontWeight: 400, fontSize: "1rem", color: "rgba(255,255,255,0.25)", lineHeight: 1 }}>
                /{String(TOTAL).padStart(2, "0")}
              </span>
            </div>

            <div className="flex items-center gap-2">
              <button
                type="button"
                onClick={() => goTo(activeIndex - 1)}
                disabled={activeIndex === 0 || TOTAL <= 1}
                className="flex items-center justify-center transition-all duration-300"
                style={{
                  width: 48,
                  height: 48,
                  border: "1px solid rgba(255,255,255,0.12)",
                  background: activeIndex === 0 || TOTAL <= 1 ? "transparent" : "rgba(168,85,247,0.08)",
                  color: activeIndex === 0 || TOTAL <= 1 ? "rgba(255,255,255,0.2)" : "#fff",
                  cursor: activeIndex === 0 || TOTAL <= 1 ? "not-allowed" : "pointer",
                }}
              >
                <ChevronLeft size={20} />
              </button>
              <button
                type="button"
                onClick={() => goTo(activeIndex + 1)}
                disabled={activeIndex === TOTAL - 1 || TOTAL <= 1}
                className="flex items-center justify-center transition-all duration-300"
                style={{
                  width: 48,
                  height: 48,
                  border: "1px solid rgba(255,255,255,0.12)",
                  background: activeIndex === TOTAL - 1 || TOTAL <= 1 ? "transparent" : "rgba(168,85,247,0.08)",
                  color: activeIndex === TOTAL - 1 || TOTAL <= 1 ? "rgba(255,255,255,0.2)" : "#fff",
                  cursor: activeIndex === TOTAL - 1 || TOTAL <= 1 ? "not-allowed" : "pointer",
                }}
              >
                <ChevronRight size={20} />
              </button>
            </div>

            <a
              href="/discover"
              className="flex items-center gap-2 px-4 py-3 md:px-6 transition-all duration-300 whitespace-nowrap"
              style={{
                border: "1px solid rgba(168,85,247,0.3)",
                fontFamily: "'Barlow Condensed', sans-serif",
                fontWeight: 700,
                fontSize: "11px",
                letterSpacing: "0.25em",
                color: "#A855F7",
                background: "rgba(168,85,247,0.05)",
                cursor: "pointer",
                textDecoration: "none",
              }}
            >
              ALL EVENTS
              <ArrowRight size={12} />
            </a>
          </motion.div>
        </div>

        <div
          className="relative overflow-hidden"
          style={{ paddingLeft: "clamp(2rem, 4vw, 8rem)" }}
        >
          <motion.div
            drag="x"
            style={{ x, display: "flex", gap: GAP, cursor: TOTAL > 1 ? "grab" : "default" }}
            dragConstraints={{ left: maxDrag, right: 0 }}
            dragElastic={0.08}
            onDragEnd={handleDragEnd}
            whileTap={{ cursor: TOTAL > 1 ? "grabbing" : "default" }}
          >
            {isLoading && (
              <EventsSectionState
                title="Loading upcoming shows"
                body="Fetching the latest published events from the Black Sky event catalog."
              />
            )}

            {isError && (
              <EventsSectionState
                title="Events unavailable"
                body="The event catalog could not be loaded right now. The full listing is still available from Discover."
              />
            )}

            {!isLoading && !isError && TOTAL === 0 && (
              <EventsSectionState
                title="No upcoming shows yet"
                body="Published events with future dates will appear here automatically after they are added from admin."
              />
            )}

            {!isLoading && !isError && events.map((event, index) => (
              <EventSlideCard
                key={event.id}
                event={event}
                isActive={index === activeIndex}
              />
            ))}
            <div style={{ width: "clamp(2rem, 4vw, 6rem)", flexShrink: 0 }} />
          </motion.div>
        </div>

        <div className="px-8 md:px-16 mt-10 flex items-center justify-between gap-8">
          <div
            className="flex-1 relative overflow-hidden"
            style={{ height: 2, background: "rgba(255,255,255,0.07)", maxWidth: 320 }}
          >
            <motion.div
              animate={{ width: `${progressPct}%` }}
              transition={{ type: "spring", stiffness: 280, damping: 28 }}
              style={{
                position: "absolute",
                left: 0,
                top: 0,
                height: "100%",
                background: "linear-gradient(90deg, #A855F7, #0EA5E9)",
              }}
            />
          </div>

          <div className="flex items-center gap-2">
            {events.map((event, i) => (
              <button
                key={event.id}
                type="button"
                aria-label={`Show ${event.title}`}
                onClick={() => goTo(i)}
                style={{
                  width: i === activeIndex ? 24 : 8,
                  height: 6,
                  background: i === activeIndex
                    ? "linear-gradient(90deg, #A855F7, #0EA5E9)"
                    : "rgba(255,255,255,0.15)",
                  border: "none",
                  cursor: "pointer",
                  transition: "width 0.35s ease, background 0.35s ease",
                  padding: 0,
                }}
              />
            ))}
          </div>

          <span
            style={{
              fontFamily: "'Barlow Condensed', sans-serif",
              fontWeight: 600,
              fontSize: "10px",
              letterSpacing: "0.3em",
              color: "rgba(255,255,255,0.2)",
            }}
          >
            DRAG TO EXPLORE
          </span>
        </div>
      </div>
    </section>
  );
}
