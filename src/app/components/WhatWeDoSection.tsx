import { useRef } from "react";
import {
  FileCheck2,
  Megaphone,
  Mic2,
  Settings2,
  Sparkles,
  UserRoundCheck,
  type LucideIcon,
} from "lucide-react";
import { motion, useInView } from "motion/react";

type ServiceGroup = {
  eyebrow: string;
  title: string;
  description: string;
  accent: string;
  Icon: LucideIcon;
  services: string[];
};

const serviceGroups: ServiceGroup[] = [
  {
    eyebrow: "Live Shows",
    title: "Concerts & Tours",
    description: "Promotion, planning, and touring support for shows that need strong coordination from announcement to show day.",
    accent: "#0EA5E9",
    Icon: Mic2,
    services: ["Concert & Tour Promotion", "Event Organiser", "Event Consultation & Advisory"],
  },
  {
    eyebrow: "Production",
    title: "Show Operations",
    description: "Hands-on production management for the stage, crew, technical flow, and venue execution.",
    accent: "#A855F7",
    Icon: Settings2,
    services: [
      "Entertainment Production Management",
      "Local Production Team Support",
      "Event Technology & Technical Direction",
    ],
  },
  {
    eyebrow: "Talent",
    title: "Artist Relations",
    description: "Reliable artist handling, booking coordination, and on-ground liaison support for a smooth guest experience.",
    accent: "#F97316",
    Icon: UserRoundCheck,
    services: ["Artist / Talent Management & Booking", "Artist Liaison Services", "Usher & Event Security"],
  },
  {
    eyebrow: "Media",
    title: "Press & Promotion",
    description: "Campaign, media, and public-facing support that helps the event reach the right audience with a clear message.",
    accent: "#22D3EE",
    Icon: Megaphone,
    services: ["Media Press Conference Coordination", "Public Relations & Promotion", "Photography & Videography"],
  },
  {
    eyebrow: "Creative",
    title: "Brand & Creative",
    description: "Creative development and brand activation support for partners who want the experience to feel distinctive.",
    accent: "#FACC15",
    Icon: Sparkles,
    services: ["Creative Services", "Brand Collaboration & Activation"],
  },
  {
    eyebrow: "Compliance",
    title: "Permits & Licensing",
    description: "Event permit coordination with the relevant authorities so the production can move forward with confidence.",
    accent: "#34D399",
    Icon: FileCheck2,
    services: ["Permit & License Coordination for Events & Concerts", "PUSPAL & Local Authority Arrangements"],
  },
];

export function WhatWeDoSection() {
  const sectionRef = useRef<HTMLElement>(null);
  const isInView = useInView(sectionRef, { once: true, margin: "-120px" });

  return (
    <section
      id="services"
      ref={sectionRef}
      className="relative overflow-hidden py-24 md:py-28"
      style={{
        background:
          "linear-gradient(180deg, #050505 0%, #08070d 48%, #050505 100%)",
        borderTop: "1px solid rgba(255,255,255,0.06)",
      }}
    >
      <div
        className="absolute"
        style={{
          width: "760px",
          height: "760px",
          left: "-260px",
          top: "120px",
          background: "radial-gradient(circle, rgba(168,85,247,0.08) 0%, transparent 70%)",
          pointerEvents: "none",
        }}
      />
      <div
        className="absolute"
        style={{
          width: "580px",
          height: "580px",
          right: "-180px",
          bottom: "-160px",
          background: "radial-gradient(circle, rgba(14,165,233,0.08) 0%, transparent 70%)",
          pointerEvents: "none",
        }}
      />

      <div className="relative max-w-[1600px] mx-auto px-8 md:px-16">
        <motion.div
          initial={{ opacity: 0, y: 24 }}
          animate={isInView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.65, ease: [0.22, 1, 0.36, 1] }}
          className="grid grid-cols-1 xl:grid-cols-[0.8fr_1.2fr] gap-8 xl:gap-16 items-end mb-12"
        >
          <div>
            <div className="flex items-center gap-4 mb-8">
              <div
                style={{
                  width: 32,
                  height: 2,
                  background: "linear-gradient(90deg, #A855F7, transparent)",
                }}
              />
              <span
                style={{
                  fontFamily: "'Barlow Condensed', sans-serif",
                  fontWeight: 600,
                  fontSize: "12px",
                  letterSpacing: "0.4em",
                  color: "#A855F7",
                }}
              >
                OUR SERVICES
              </span>
            </div>

            <h2
              style={{
                fontFamily: "'Barlow Condensed', sans-serif",
                fontWeight: 900,
                fontSize: "clamp(3.6rem, 7vw, 7.4rem)",
                lineHeight: 0.88,
                letterSpacing: "0",
                color: "#FFFFFF",
                textTransform: "uppercase",
              }}
            >
              WHAT WE DO
            </h2>
          </div>

          <div className="max-w-[760px] xl:ml-auto">
            <p
              style={{
                fontFamily: "'Barlow', sans-serif",
                fontWeight: 300,
                fontSize: "clamp(1rem, 1.35vw, 1.18rem)",
                lineHeight: 1.8,
                color: "rgba(255,255,255,0.58)",
                margin: 0,
              }}
            >
              We are committed to delivering professional entertainment experiences through strong coordination, creative production,
              and reliable event management. Our goal is to create memorable events that connect artists, brands, and audiences while
              maintaining high industry standards.
            </p>
          </div>
        </motion.div>

        <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 xl:gap-6">
          {serviceGroups.map(({ eyebrow, title, description, accent, Icon, services }, index) => (
            <motion.article
              key={title}
              initial={{ opacity: 0, y: 28 }}
              animate={isInView ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.7, delay: 0.08 * index, ease: [0.22, 1, 0.36, 1] }}
              className="group relative overflow-hidden"
              style={{
                minHeight: "304px",
                border: "1px solid rgba(255,255,255,0.09)",
                borderRadius: "8px",
                background:
                  "linear-gradient(135deg, rgba(255,255,255,0.055), rgba(255,255,255,0.018))",
                padding: "clamp(20px, 2vw, 28px)",
              }}
            >
              <div
                className="absolute inset-x-0 top-0 h-px"
                style={{
                  background: `linear-gradient(90deg, transparent, ${accent}, transparent)`,
                  opacity: 0.8,
                }}
              />
              <div
                className="absolute -right-16 -top-16"
                style={{
                  width: 220,
                  height: 220,
                  background: `radial-gradient(circle, ${accent}26 0%, transparent 68%)`,
                  pointerEvents: "none",
                }}
              />

              <div className="relative z-10 flex h-full flex-col">
                <div className="flex items-start justify-between gap-5 mb-6">
                  <div
                    className="grid place-items-center"
                    style={{
                      width: 44,
                      height: 44,
                      border: `1px solid ${accent}66`,
                      background: `${accent}12`,
                    }}
                  >
                    <Icon size={20} strokeWidth={1.7} style={{ color: accent }} aria-hidden="true" />
                  </div>
                  <span
                    style={{
                      fontFamily: "'Barlow Condensed', sans-serif",
                      fontWeight: 800,
                      fontSize: "0.78rem",
                      letterSpacing: "0.28em",
                      color: "rgba(255,255,255,0.36)",
                    }}
                  >
                    {String(index + 1).padStart(2, "0")}
                  </span>
                </div>

                <span
                  style={{
                    fontFamily: "'Barlow Condensed', sans-serif",
                    fontWeight: 700,
                    fontSize: "0.72rem",
                    letterSpacing: "0.34em",
                    color: accent,
                    textTransform: "uppercase",
                  }}
                >
                  {eyebrow}
                </span>
                <h3
                  style={{
                    fontFamily: "'Barlow Condensed', sans-serif",
                    fontWeight: 900,
                    fontSize: "clamp(1.8rem, 2.6vw, 2.7rem)",
                    lineHeight: 0.92,
                    letterSpacing: "0",
                    color: "#FFFFFF",
                    textTransform: "uppercase",
                    margin: "9px 0 12px",
                  }}
                >
                  {title}
                </h3>
                <p
                  style={{
                    fontFamily: "'Barlow', sans-serif",
                    fontWeight: 300,
                    fontSize: "0.94rem",
                    lineHeight: 1.72,
                    color: "rgba(255,255,255,0.52)",
                    margin: "0 0 18px",
                  }}
                >
                  {description}
                </p>

                <ul className="mt-auto grid gap-3" style={{ listStyle: "none", padding: 0, marginBottom: 0 }}>
                  {services.map((service) => (
                    <li
                      key={service}
                      className="flex items-start gap-3"
                      style={{
                        fontFamily: "'Barlow', sans-serif",
                        fontSize: "0.92rem",
                        lineHeight: 1.45,
                        color: "rgba(255,255,255,0.74)",
                      }}
                    >
                      <span
                        aria-hidden="true"
                        style={{
                          width: 7,
                          height: 7,
                          marginTop: 7,
                          background: accent,
                          boxShadow: `0 0 18px ${accent}`,
                          flexShrink: 0,
                        }}
                      />
                      {service}
                    </li>
                  ))}
                </ul>
              </div>
            </motion.article>
          ))}
        </div>

      </div>
    </section>
  );
}
