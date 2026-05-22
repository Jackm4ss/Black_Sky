import { useRef } from "react";
import { motion, useScroll, useTransform } from "motion/react";
import { ArrowRight, ArrowUpRight } from "lucide-react";
import { useCurrentUser } from "../auth/auth-queries";
import LogoLoop, { type LogoItem } from "./LogoLoop";
import heroImage from "../../assets/hero-concert-bg.png";

const tickerItems = [
  "SOLD OUT SHOWS",
  "★",
  "KUALA LUMPUR",
  "★",
  "SINGAPORE",
  "★",
  "JAKARTA",
  "★",
  "BANGKOK",
  "★",
  "MANILA",
  "★",
  "HO CHI MINH",
  "★",
  "2M+ FANS",
  "★",
  "LIVE EXPERIENCES",
  "★",
];

const partnerMark = (label: string, color = "rgba(235,245,255,0.74)") => (
  <span
    style={{
      display: "inline-flex",
      alignItems: "center",
      fontFamily: "'Barlow Condensed', sans-serif",
      fontWeight: 800,
      fontSize: "0.72em",
      letterSpacing: "0.18em",
      lineHeight: 1,
      color,
      textTransform: "uppercase",
      whiteSpace: "nowrap",
    }}
  >
    {label}
  </span>
);

const partnerLogos: LogoItem[] = [
  { node: partnerMark("AXIATA ARENA"), title: "Axiata Arena" },
  { node: partnerMark("ZEPP KL", "#A855F7"), title: "Zepp Kuala Lumpur" },
  { node: partnerMark("TICKET2U"), title: "Ticket2U" },
  { node: partnerMark("HITZ", "#0EA5E9"), title: "HITZ" },
  { node: partnerMark("ASTRO"), title: "Astro" },
  { node: partnerMark("SPOTV", "#F97316"), title: "SPOTV" },
  { node: partnerMark("TIMEOUT KL"), title: "TimeOut KL" },
  { node: partnerMark("AIRASIA", "#E11D48"), title: "AirAsia" },
];

export function HeroSection() {
  const containerRef = useRef<HTMLDivElement>(null);
  const { data: user } = useCurrentUser();
  const { scrollY } = useScroll();
  const bgY = useTransform(scrollY, [0, 800], [0, 200]);
  const opacity = useTransform(scrollY, [0, 500], [1, 0]);

  return (
    <section
      ref={containerRef}
      className="relative min-h-screen flex flex-col overflow-hidden"
      style={{ background: "#050505" }}
    >
      {/* Parallax BG */}
      <motion.div
        className="absolute inset-0"
        style={{ y: bgY }}
      >
        <img
          src={heroImage}
          alt="Concert crowd"
          className="w-full h-full object-cover"
          style={{
            objectPosition: "center center",
            transform: "scale(1.04)",
            transformOrigin: "center top",
          }}
        />
        {/* Base dark overlay */}
        <div
          className="absolute inset-0"
          style={{
            background:
              "linear-gradient(180deg, rgba(5,5,5,0.35) 0%, rgba(5,5,5,0.2) 40%, rgba(5,5,5,0.85) 80%, rgba(5,5,5,1) 100%)",
          }}
        />
        {/* Left vignette */}
        <div
          className="absolute inset-0"
          style={{
            background:
              "linear-gradient(90deg, rgba(5,5,5,0.8) 0%, transparent 50%)",
          }}
        />
        {/* Glow orbs */}
        <div
          className="absolute"
          style={{
            width: "600px",
            height: "600px",
            top: "10%",
            right: "-10%",
            background: "radial-gradient(circle, rgba(168,85,247,0.25) 0%, transparent 70%)",
            pointerEvents: "none",
          }}
        />
        <div
          className="absolute"
          style={{
            width: "500px",
            height: "500px",
            bottom: "20%",
            left: "5%",
            background: "radial-gradient(circle, rgba(14,165,233,0.2) 0%, transparent 70%)",
            pointerEvents: "none",
          }}
        />
        <div
          className="absolute"
          style={{
            width: "400px",
            height: "400px",
            top: "30%",
            left: "40%",
            background: "radial-gradient(circle, rgba(225,29,72,0.12) 0%, transparent 70%)",
            pointerEvents: "none",
          }}
        />
      </motion.div>

      {/* Content */}
      <motion.div
        style={{ opacity }}
        className="relative z-10 flex-1 flex flex-col justify-center lg:justify-end pt-24 pb-24 lg:pt-0 lg:pb-32 px-8 md:px-16 max-w-[1600px] mx-auto w-full"
      >
        {/* Label */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.5, duration: 0.8 }}
          className="flex items-center gap-4 mb-6"
        >
          <div
            style={{
              width: 32,
              height: 2,
              flexShrink: 0,
              background: "#2E7BCB",
            }}
          />
          <span
            style={{
              fontFamily: "'Barlow Condensed', sans-serif",
              fontWeight: 600,
              fontSize: "clamp(8px, 1.6vw, 12px)",
              letterSpacing: "clamp(0.18em, 0.45vw, 0.34em)",
              color: "rgba(255,255,255,0.72)",
              whiteSpace: "nowrap",
            }}
          >
            MALAYSIA'S PREMIER CONCERT PROMOTER · EST. 2016
          </span>
        </motion.div>

        {/* Main Headline */}
        <motion.h1
          initial={{ opacity: 0, y: 60 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.7, duration: 1, ease: [0.22, 1, 0.36, 1] }}
          style={{
            fontFamily: "'Barlow Condensed', sans-serif",
            fontWeight: 900,
            fontSize: "clamp(3.5rem, 17vw, 13rem)",
            lineHeight: 0.76,
            letterSpacing: "-0.025em",
            color: "#FFFFFF",
            textTransform: "uppercase",
            maxWidth: "1200px",
          }}
        >
          <span style={{ display: "block" }}>BLACK</span>
          <span style={{ display: "block" }}>SKY</span>
          <span style={{ display: "block" }}>ENTERPRISE</span>
        </motion.h1>

        {/* Sub + CTA Row */}
        <motion.div
          initial={{ opacity: 0, y: 40 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 1.0, duration: 0.8 }}
          className="flex flex-col md:flex-row items-start md:items-end justify-between gap-8 mt-10"
        >
          <p
            style={{
              fontFamily: "'Barlow', sans-serif",
              fontWeight: 300,
              fontSize: "clamp(0.9rem, 1.5vw, 1.15rem)",
              lineHeight: 1.6,
              color: "rgba(255,255,255,0.55)",
              maxWidth: "420px",
            }}
          >
            Black Sky Enterprise is a leading concert promoter and event management company delivering unforgettable live experiences across Malaysia and beyond.
          </p>

          <div className="flex items-center gap-3 sm:gap-4 flex-shrink-0">
            <motion.a
              href="/discover"
              className="flex items-center gap-2 sm:gap-3 px-5 py-3 sm:px-8 sm:py-4 transition-all duration-300"
              style={{
                border: "none",
                fontFamily: "'Barlow Condensed', sans-serif",
                fontWeight: 700,
                fontSize: "clamp(10.5px, 2vw, 12px)",
                letterSpacing: "0.25em",
                color: "#030213",
                background: "#FFFFFF",
                cursor: "pointer",
                flexShrink: 0,
                lineHeight: 1,
                textDecoration: "none",
              }}
              whileHover={{ opacity: 0.9 }}
            >
              EXPLORE MORE
              <ArrowRight size={12} />
            </motion.a>

            {!user ? (
              <motion.a
                href="/register"
                className="relative z-10 flex items-center gap-2 sm:gap-3 px-5 py-3 sm:px-8 sm:py-4 transition-all duration-300"
                style={{
                  background: "#0EA5E9",
                  fontFamily: "'Barlow Condensed', sans-serif",
                  fontWeight: 700,
                  fontSize: "clamp(10.5px, 2vw, 12px)",
                  letterSpacing: "0.25em",
                  color: "#fff",
                  border: "none",
                  cursor: "pointer",
                  flexShrink: 0,
                  lineHeight: 1,
                  textDecoration: "none",
                }}
                whileHover={{ opacity: 0.9 }}
              >
                GET TICKET
                <ArrowUpRight size={12} />
              </motion.a>
            ) : null}
          </div>
        </motion.div>
      </motion.div>

      {/* Scroll Indicator */}
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        transition={{ delay: 1.8, duration: 0.8 }}
        className="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center gap-2"
      >
        <span
          style={{
            fontFamily: "'Barlow Condensed', sans-serif",
            fontSize: "10px",
            letterSpacing: "0.4em",
            color: "rgba(255,255,255,0.3)",
          }}
        >
          SCROLL
        </span>
        <motion.div
          animate={{ y: [0, 8, 0] }}
          transition={{ duration: 1.5, repeat: Infinity, ease: "easeInOut" }}
          style={{
            width: 1,
            height: 40,
            background: "linear-gradient(180deg, rgba(168,85,247,0.8) 0%, transparent 100%)",
          }}
        />
      </motion.div>

      {/* Bottom Ticker */}
      <div
        className="absolute bottom-0 left-0 right-0 z-10 flex overflow-hidden"
        style={{
          borderTop: "1px solid rgba(255,255,255,0.06)",
          background: "rgba(5,5,5,0.8)",
          backdropFilter: "blur(10px)",
        }}
      >
        <div
          className="relative z-20 flex flex-shrink-0 items-center px-5 sm:px-7"
          style={{
            background: "rgba(3,7,12,0.92)",
            borderRight: "1px solid rgba(14,165,233,0.28)",
            boxShadow: "18px 0 35px rgba(3,7,12,0.82)",
          }}
        >
          <span
            style={{
              fontFamily: "'Barlow Condensed', sans-serif",
              fontWeight: 800,
              fontSize: "clamp(10px, 2vw, 12px)",
              letterSpacing: "0.18em",
              lineHeight: 1,
              color: "rgba(235,245,255,0.9)",
              textTransform: "uppercase",
              textShadow:
                "0 0 8px rgba(14,165,233,0.75), 0 0 18px rgba(14,165,233,0.35)",
              whiteSpace: "nowrap",
            }}
          >
            TRUSTED BY PARTNERS
          </span>
        </div>

        <div className="relative min-w-0 flex-1 overflow-hidden py-2">
          <div
            className="pointer-events-none absolute inset-y-0 left-0 z-10 w-16"
            style={{
              background:
                "linear-gradient(90deg, rgba(3,7,12,0.95) 0%, rgba(3,7,12,0.55) 45%, transparent 100%)",
            }}
          />
          <LogoLoop
            logos={partnerLogos}
            speed={84}
            direction="left"
            logoHeight={26}
            gap={46}
            hoverSpeed={18}
            fadeOut
            fadeOutColor="#03070c"
            scaleOnHover
            ariaLabel="Trusted partners"
          />
          <div
          className="hidden"
          style={{
            animation: "ticker 30s linear infinite",
            whiteSpace: "nowrap",
            willChange: "transform",
          }}
        >
          {[...tickerItems, ...tickerItems, ...tickerItems].map((item, i) => (
            <span
              key={i}
              className="mx-4"
              style={{
                fontFamily: "'Barlow Condensed', sans-serif",
                fontWeight: 600,
                fontSize: "11px",
                letterSpacing: "0.3em",
                color: item === "★" ? "#A855F7" : "rgba(255,255,255,0.35)",
              }}
            >
              {item}
            </span>
          ))}
          </div>
        </div>
      </div>

      <style>{`
        @keyframes ticker {
          from { transform: translateX(0); }
          to { transform: translateX(-33.333%); }
        }
      `}</style>
    </section>
  );
}
