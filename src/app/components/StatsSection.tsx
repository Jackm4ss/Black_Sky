import { useRef, useEffect, useState } from "react";
import { motion, useInView } from "motion/react";
import DotGrid from "./DotGrid";
import GradientText from "./GradientText";

interface Stat {
  value: number;
  suffix: string;
  prefix?: string;
  label: string;
  sublabel: string;
  color: string;
  gradientFrom: string;
  gradientTo: string;
}

const stats: Stat[] = [
  {
    value: 500,
    suffix: "+",
    label: "SHOWS",
    sublabel: "Concerts & Festivals Produced",
    color: "#A855F7",
    gradientFrom: "#A855F7",
    gradientTo: "#7C3AED",
  },
  {
    value: 2,
    suffix: "M+",
    label: "FANS REACHED",
    sublabel: "Across Southeast Asia",
    color: "#0EA5E9",
    gradientFrom: "#0EA5E9",
    gradientTo: "#0284C7",
  },
  {
    value: 8,
    suffix: "",
    label: "COUNTRIES",
    sublabel: "Active Market Presence",
    color: "#F97316",
    gradientFrom: "#F97316",
    gradientTo: "#EA580C",
  },
  {
    value: 150,
    suffix: "+",
    label: "ARTISTS",
    sublabel: "International & Regional Acts",
    color: "#E11D48",
    gradientFrom: "#E11D48",
    gradientTo: "#BE123C",
  },
  {
    value: 98,
    suffix: "%",
    label: "SOLD OUT",
    sublabel: "Average Show Fill Rate",
    color: "#FFB700",
    gradientFrom: "#FFB700",
    gradientTo: "#F59E0B",
  },
  {
    value: 10,
    suffix: "+",
    label: "YEARS",
    sublabel: "Of Industry Excellence",
    color: "#10B981",
    gradientFrom: "#10B981",
    gradientTo: "#059669",
  },
];

function AnimatedNumber({ value, suffix, prefix, inView }: {
  value: number;
  suffix: string;
  prefix?: string;
  inView: boolean;
}) {
  const [current, setCurrent] = useState(0);

  useEffect(() => {
    if (!inView) return;
    const duration = 2000;
    const steps = 60;
    const increment = value / steps;
    let count = 0;
    const timer = setInterval(() => {
      count += increment;
      if (count >= value) {
        setCurrent(value);
        clearInterval(timer);
      } else {
        setCurrent(Math.floor(count));
      }
    }, duration / steps);
    return () => clearInterval(timer);
  }, [inView, value]);

  return (
    <span
      style={{
        fontFamily: "'Barlow Condensed', sans-serif",
        fontWeight: 900,
        fontSize: "clamp(3rem, 5vw, 6rem)",
        lineHeight: 1,
        color: "#FFFFFF",
      }}
    >
      {prefix}{current}{suffix}
    </span>
  );
}

export function StatsSection() {
  const sectionRef = useRef<HTMLDivElement>(null);
  const isInView = useInView(sectionRef, { once: true, margin: "-100px" });

  return (
    <section
      ref={sectionRef}
      className="relative py-32 overflow-hidden"
      style={{ background: "#0A0A0A" }}
    >
      {/* Top border glow */}
      <div
        className="absolute top-0 left-0 right-0"
        style={{
          height: "1px",
          background: "linear-gradient(90deg, transparent, rgba(14,165,233,0.4), rgba(168,85,247,0.4), transparent)",
        }}
      />

      {/* Interactive React Bits dot-grid background */}
      <div className="absolute inset-0 opacity-70">
        <DotGrid
          dotSize={3}
          gap={28}
          baseColor="#123A56"
          activeColor="#0EA5E9"
          proximity={120}
          speedTrigger={100}
          shockRadius={250}
          shockStrength={5}
          resistance={750}
          returnDuration={1.5}
        />
      </div>
      <div
        className="absolute inset-0 pointer-events-none"
        style={{
          background:
            "radial-gradient(circle at 50% 22%, rgba(14,165,233,0.14), transparent 34%), linear-gradient(180deg, rgba(10,10,10,0.16) 0%, rgba(10,10,10,0.72) 100%)",
        }}
      />

      <div className="max-w-[1600px] mx-auto px-8 md:px-16 relative z-10">
        {/* Header */}
        <div className="text-center mb-20">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={isInView ? { opacity: 1, y: 0 } : {}}
            transition={{ duration: 0.6 }}
            className="flex items-center justify-center gap-4 mb-4"
          >
            <div
              style={{
                width: 32,
                height: 2,
                background: "linear-gradient(90deg, transparent, #0EA5E9)",
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
              BY THE NUMBERS
            </span>
            <div
              style={{
                width: 32,
                height: 2,
                background: "linear-gradient(90deg, #0EA5E9, transparent)",
              }}
            />
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
            OUR IMPACT
            <br />
            <GradientText
              colors={["#0EA5E9", "#A855F7", "#0EA5E9", "#A855F7", "#0EA5E9"]}
              animationSpeed={3}
              showBorder={false}
            >
              IN NUMBERS
            </GradientText>
          </motion.h2>
        </div>

        {/* Stats Grid */}
        <div className="grid grid-cols-2 md:grid-cols-3 gap-px">
          {stats.map((stat, i) => (
            <motion.div
              key={stat.label}
              initial={{ opacity: 0, y: 30 }}
              animate={isInView ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.6, delay: 0.1 + i * 0.07 }}
              className="flex flex-col items-start p-8 md:p-12 relative group"
              style={{
                background: "rgba(5,16,29,0.72)",
                border: "1px solid rgba(255,255,255,0.08)",
                backdropFilter: "blur(12px)",
              }}
            >
              {/* Glow on hover */}
              <div
                className="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500"
                style={{
                  background: `radial-gradient(circle at bottom left, ${stat.color}12 0%, transparent 70%)`,
                  pointerEvents: "none",
                }}
              />

              {/* Number */}
              <AnimatedNumber
                value={stat.value}
                suffix={stat.suffix}
                prefix={stat.prefix}
                inView={isInView}
              />

              {/* Label */}
              <h3
                style={{
                  fontFamily: "'Barlow Condensed', sans-serif",
                  fontWeight: 800,
                  fontSize: "1.1rem",
                  letterSpacing: "0.15em",
                  color: "#FFFFFF",
                  textTransform: "uppercase",
                  marginTop: "8px",
                  marginBottom: "4px",
                }}
              >
                {stat.label}
              </h3>
              <p
                style={{
                  fontFamily: "'Barlow', sans-serif",
                  fontWeight: 300,
                  fontSize: "0.78rem",
                  color: "rgba(255,255,255,0.3)",
                  letterSpacing: "0.05em",
                }}
              >
                {stat.sublabel}
              </p>

              {/* Accent bar */}
              <div
                className="absolute bottom-0 left-0 w-0 group-hover:w-full transition-all duration-700"
                style={{ height: "2px", background: `linear-gradient(90deg, ${stat.color}, transparent)` }}
              />
            </motion.div>
          ))}
        </div>

        {/* Bottom CTA Quote */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          animate={isInView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.8, delay: 0.5 }}
          className="text-center mt-20"
        >
          <p
            style={{
              fontFamily: "'Barlow Condensed', sans-serif",
              fontStyle: "italic",
              fontWeight: 700,
              fontSize: "clamp(1.3rem, 3vw, 2.5rem)",
              color: "rgba(255,255,255,0.2)",
              letterSpacing: "0.02em",
            }}
          >
            "we bring{" "}
            <GradientText
              colors={["#A855F7", "#0EA5E9", "#A855F7", "#0EA5E9", "#A855F7"]}
              animationSpeed={3}
              showBorder={false}
            >
              unforgettable experiences
            </GradientText>
            {" "}to life."
          </p>
        </motion.div>
      </div>

      {/* Bottom border glow */}
      <div
        className="absolute bottom-0 left-0 right-0"
        style={{
          height: "1px",
          background: "linear-gradient(90deg, transparent, rgba(168,85,247,0.4), rgba(14,165,233,0.4), transparent)",
        }}
      />
    </section>
  );
}
