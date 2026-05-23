import { useRef } from "react";
import { motion, useInView } from "motion/react";
import GradientText from "./GradientText";
import productionHeroImage from "../../assets/hero-concert-bg.png";

const stageLightingImage =
  "https://images.unsplash.com/photo-1765224747205-3c9c23f0553c?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjb25jZXJ0JTIwc3RhZ2UlMjBsaWdodGluZyUyMGNyb3dkJTIwZGFyayUyMGRyYW1hdGljfGVufDF8fHx8MTc3ODIzOTQxNnww&ixlib=rb-4.1.0&q=80&w=1080";
const showOperationsImage =
  "https://images.unsplash.com/photo-1774112560513-38b6ec3ba898?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxlbGVjdHJvbmljJTIwbXVzaWMlMjBmZXN0aXZhbCUyMGNvbG9yZnVsJTIwbGFzZXIlMjBzaG93fGVufDF8fHx8MTc3ODIzOTQxN3ww&ixlib=rb-4.1.0&q=80&w=1080";

const productionImages = [
  {
    src: productionHeroImage,
    alt: "Concert production stage with crowd",
    label: "Stage Production",
    meta: "Arena Scale",
    accent: "#0EA5E9",
    className: "sm:row-span-2",
  },
  {
    src: stageLightingImage,
    alt: "Concert lighting and stage rig",
    label: "Lighting Design",
    meta: "Show Systems",
    accent: "#A855F7",
    className: "",
  },
  {
    src: showOperationsImage,
    alt: "Festival laser and visual production",
    label: "Live Execution",
    meta: "Festival Ops",
    accent: "#F97316",
    className: "",
  },
];

const highlights = [
  { label: "Founded", value: "2015" },
  { label: "HQ", value: "Kuala Lumpur" },
  { label: "Focus", value: "SEA Region" },
];

function ProductionImageTile({
  image,
  delay,
}: {
  image: (typeof productionImages)[number];
  delay: number;
}) {
  return (
    <motion.div
      initial={{ opacity: 0, y: 24 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.7, delay, ease: [0.22, 1, 0.36, 1] }}
      className={`relative overflow-hidden ${image.className}`}
      style={{
        minHeight: image.className ? "520px" : "252px",
        border: "1px solid rgba(255,255,255,0.08)",
      }}
    >
      <img
        src={image.src}
        alt={image.alt}
        className="absolute inset-0 w-full h-full object-cover"
        style={{
          transition: "transform 0.7s ease",
        }}
        onMouseEnter={(e) =>
          ((e.currentTarget as HTMLImageElement).style.transform = "scale(1.04)")
        }
        onMouseLeave={(e) =>
          ((e.currentTarget as HTMLImageElement).style.transform = "scale(1)")
        }
      />
      <div
        className="absolute inset-0"
        style={{
          background:
            "linear-gradient(180deg, rgba(5,5,5,0.08) 0%, rgba(5,5,5,0.52) 45%, rgba(5,5,5,0.94) 100%)",
        }}
      />
      <div
        className="absolute top-0 left-0"
        style={{
          width: "60px",
          height: "60px",
          borderTop: `2px solid ${image.accent}`,
          borderLeft: `2px solid ${image.accent}`,
        }}
      />
      <div className="absolute bottom-5 left-5 right-5">
        <div
          style={{
            width: 34,
            height: 2,
            background: image.accent,
            marginBottom: 12,
          }}
        />
        <span
          style={{
            fontFamily: "'Barlow Condensed', sans-serif",
            fontWeight: 800,
            fontSize: image.className ? "clamp(1.8rem, 3vw, 3rem)" : "1.35rem",
            lineHeight: 0.95,
            color: "#fff",
            textTransform: "uppercase",
            display: "block",
          }}
        >
          {image.label}
        </span>
        <span
          style={{
            fontFamily: "'Barlow Condensed', sans-serif",
            fontWeight: 600,
            fontSize: "0.72rem",
            letterSpacing: "0.26em",
            color: "rgba(255,255,255,0.56)",
            textTransform: "uppercase",
          }}
        >
          {image.meta}
        </span>
      </div>
    </motion.div>
  );
}

export function AboutSection() {
  const sectionRef = useRef<HTMLDivElement>(null);
  const isInView = useInView(sectionRef, { once: true, margin: "-100px" });

  return (
    <section
      id="about"
      ref={sectionRef}
      className="relative overflow-hidden py-32"
      style={{ background: "#050505" }}
    >
      {/* Glow BG */}
      <div
        className="absolute"
        style={{
          width: "700px",
          height: "700px",
          top: "-100px",
          right: "-200px",
          background: "radial-gradient(circle, rgba(14,165,233,0.07) 0%, transparent 70%)",
          pointerEvents: "none",
        }}
      />

      <div className="max-w-[1600px] mx-auto px-8 md:px-16">
        {/* Section Label */}
        <motion.div
          initial={{ opacity: 0, x: -30 }}
          animate={isInView ? { opacity: 1, x: 0 } : {}}
          transition={{ duration: 0.6 }}
          className="flex items-center gap-4 mb-20"
        >
          <div
            style={{
              width: 32,
              height: 2,
              background: "linear-gradient(90deg, #0EA5E9, transparent)",
            }}
          />
          <span
            style={{
              fontFamily: "'Barlow Condensed', sans-serif",
              fontWeight: 600,
              fontSize: "12px",
              letterSpacing: "0.4em",
              color: "#0EA5E9",
            }}
          >
            WHO WE ARE
          </span>
        </motion.div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 xl:gap-24 items-center">
          {/* Left: Text Content */}
          <div>
            {/* EST tag */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={isInView ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.5, delay: 0.1 }}
              className="inline-flex items-center gap-2 mb-8"
              style={{
                border: "1px solid rgba(168,85,247,0.3)",
                padding: "6px 16px",
                background: "rgba(168,85,247,0.05)",
              }}
            >
              <span
                style={{
                  fontFamily: "'Barlow Condensed', sans-serif",
                  fontWeight: 600,
                  fontSize: "11px",
                  letterSpacing: "0.4em",
                  color: "#A855F7",
                }}
              >
                EST. 2015 — KUALA LUMPUR
              </span>
            </motion.div>

            {/* Headline */}
            <motion.h2
              initial={{ opacity: 0, y: 40 }}
              animate={isInView ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.8, delay: 0.2, ease: [0.22, 1, 0.36, 1] }}
              style={{
                fontFamily: "'Barlow Condensed', sans-serif",
                fontWeight: 900,
                fontSize: "clamp(2.8rem, 5vw, 6rem)",
                lineHeight: 1.02,
                letterSpacing: "-0.01em",
                color: "#FFFFFF",
                textTransform: "uppercase",
                marginBottom: "2rem",
              }}
            >
              THE FORCE{" "}
              <br />
              BEHIND{" "}
              <GradientText
                colors={["#F97316", "#FFB700", "#F97316", "#FFB700", "#F97316"]}
                animationSpeed={3}
                showBorder={false}
              >
                ASIA'S
              </GradientText>
              <br />
              BIGGEST SHOWS
            </motion.h2>

            {/* Body */}
            <motion.p
              initial={{ opacity: 0, y: 20 }}
              animate={isInView ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.7, delay: 0.35 }}
              style={{
                fontFamily: "'Barlow', sans-serif",
                fontWeight: 300,
                fontSize: "clamp(0.95rem, 1.4vw, 1.1rem)",
                lineHeight: 1.8,
                color: "rgba(255,255,255,0.5)",
                maxWidth: "480px",
                marginBottom: "1.5rem",
              }}
            >
              Black Sky Enterprise is a Malaysia-based Event & Production Management company specializing in the entertainment industry. We provide professional services for concerts, entertainment events, media activities, artist management, and brand activations with a strong focus on creativity, coordination, and high-quality execution.
            </motion.p>
            <motion.p
              initial={{ opacity: 0, y: 20 }}
              animate={isInView ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.7, delay: 0.45 }}
              style={{
                fontFamily: "'Barlow', sans-serif",
                fontWeight: 300,
                fontSize: "clamp(0.95rem, 1.4vw, 1.1rem)",
                lineHeight: 1.8,
                color: "rgba(255,255,255,0.4)",
                maxWidth: "480px",
                marginBottom: "3rem",
              }}
            >
              With experience in handling local and international entertainment projects, we work closely with artists, brands, media partners, and event organizers to deliver impactful experiences across Malaysia.
            </motion.p>

            {/* Highlights */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={isInView ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.6, delay: 0.55 }}
              className="flex gap-8 flex-wrap"
            >
              {highlights.map((h, i) => (
                <div
                  key={i}
                  className="flex flex-col gap-1"
                  style={{
                    borderLeft: "2px solid rgba(168,85,247,0.72)",
                    paddingLeft: "16px",
                  }}
                >
                  <span
                    style={{
                      fontFamily: "'Barlow Condensed', sans-serif",
                      fontWeight: 700,
                      fontSize: "10px",
                      letterSpacing: "0.35em",
                      color: "rgba(255,255,255,0.72)",
                    }}
                  >
                    {h.label.toUpperCase()}
                  </span>
                  <span
                    style={{
                      fontFamily: "'Barlow Condensed', sans-serif",
                      fontWeight: 700,
                      fontSize: "1.2rem",
                      letterSpacing: "0.05em",
                      color: "#FFFFFF",
                    }}
                  >
                    {h.value}
                  </span>
                </div>
              ))}
            </motion.div>
          </div>

          {/* Right: Production Bento */}
          <motion.div
            initial={{ opacity: 0, x: 60 }}
            animate={isInView ? { opacity: 1, x: 0 } : {}}
            transition={{ duration: 1, delay: 0.3, ease: [0.22, 1, 0.36, 1] }}
            className="grid grid-cols-1 sm:grid-cols-2 gap-4"
            style={{
              gridAutoRows: "minmax(252px, auto)",
            }}
          >
            {productionImages.map((image, i) => (
              <ProductionImageTile key={image.label} image={image} delay={0.36 + i * 0.1} />
            ))}
          </motion.div>
        </div>
      </div>
    </section>
  );
}
