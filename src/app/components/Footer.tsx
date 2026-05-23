import { motion } from "motion/react";
import { Instagram, Twitter, Youtube, Facebook, ArrowUpRight, Mail, MapPin, Phone } from "lucide-react";
import GradientText from "./GradientText";
import logo from "../../assets/LOGO.png";

const footerLinks = {
  Company: ["About Us", "Our Story", "Team", "Careers", "Press Kit"],
  Services: ["Concert Promotion", "Festival Production", "Artist Management", "Media Production", "Brand Partnerships"],
  Events: ["Upcoming Shows", "Past Events", "Venues", "Ticketing", "VIP Packages"],
  Media: ["News", "Editorial", "Photography", "Videos", "Podcast"],
};

const socials = [
  { Icon: Instagram, label: "Instagram", color: "#E11D48" },
  { Icon: Twitter, label: "Twitter / X", color: "#0EA5E9" },
  { Icon: Youtube, label: "YouTube", color: "#E11D48" },
  { Icon: Facebook, label: "Facebook", color: "#0EA5E9" },
];

const showSpotlightCta = false;

export function Footer() {
  return (
    <footer
      id="contact"
      className="relative overflow-hidden"
      style={{ background: "#030303" }}
    >
      {/* Top glow bar */}
      <div
        style={{
          height: "1px",
          background: "linear-gradient(90deg, transparent, rgba(168,85,247,0.5), rgba(14,165,233,0.5), transparent)",
        }}
      />

      {/* Newsletter / CTA Band hidden until the spotlight signup is needed again. */}
      {showSpotlightCta && (
        <div
          className="relative overflow-hidden"
          style={{
            background: "linear-gradient(135deg, rgba(168,85,247,0.08) 0%, rgba(14,165,233,0.05) 50%, rgba(225,29,72,0.06) 100%)",
            borderBottom: "1px solid rgba(255,255,255,0.05)",
          }}
        >
          <div className="max-w-[1600px] mx-auto px-8 md:px-16 py-16">
            <div className="flex flex-col md:flex-row items-start md:items-center justify-between gap-8">
              <div>
                <h3
                  style={{
                    fontFamily: "'Barlow Condensed', sans-serif",
                    fontWeight: 900,
                    fontSize: "clamp(2rem, 4vw, 4rem)",
                    lineHeight: 1.06,
                    color: "#FFFFFF",
                    textTransform: "uppercase",
                    marginBottom: "14px",
                  }}
                >
                  STAY IN THE
                  <br />
                  <GradientText
                    colors={["#A855F7", "#0EA5E9", "#A855F7", "#0EA5E9", "#A855F7"]}
                    animationSpeed={3}
                    showBorder={false}
                  >
                    SPOTLIGHT
                  </GradientText>
                </h3>
                <p
                  style={{
                    fontFamily: "'Barlow', sans-serif",
                    fontWeight: 300,
                    fontSize: "0.9rem",
                    color: "rgba(255,255,255,0.4)",
                  }}
                >
                  Get early access to tickets, exclusive news & backstage content.
                </p>
              </div>

              <div className="flex gap-0 w-full md:w-auto md:min-w-[440px]">
                <input
                  type="email"
                  placeholder="YOUR EMAIL ADDRESS"
                  className="flex-1 px-6 py-4 outline-none"
                  style={{
                    background: "rgba(255,255,255,0.04)",
                    border: "1px solid rgba(255,255,255,0.1)",
                    borderRight: "none",
                    fontFamily: "'Barlow Condensed', sans-serif",
                    fontWeight: 600,
                    fontSize: "12px",
                    letterSpacing: "0.15em",
                    color: "#fff",
                  }}
                />
                <button
                  className="px-8 py-4 transition-all duration-300 hover:opacity-90 flex-shrink-0"
                  style={{
                    background: "linear-gradient(135deg, #A855F7, #0EA5E9)",
                    fontFamily: "'Barlow Condensed', sans-serif",
                    fontWeight: 700,
                    fontSize: "12px",
                    letterSpacing: "0.25em",
                    color: "#fff",
                    border: "none",
                    cursor: "pointer",
                  }}
                >
                  SUBSCRIBE
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Main Footer */}
      <div className="relative max-w-[1600px] mx-auto px-8 md:px-16 py-20">
        <img
          src={logo}
          alt=""
          aria-hidden="true"
          className="pointer-events-none absolute left-1/2 top-1/2 h-[clamp(420px,86vw,620px)] w-auto max-w-none -translate-x-1/2 -translate-y-1/2 select-none md:h-[clamp(500px,58vw,700px)] lg:h-[clamp(240px,34vw,520px)]"
          style={{
            opacity: 0.04,
            filter: "grayscale(1)",
            zIndex: 0,
          }}
        />

        <div className="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8">
          {/* Brand col */}
          <div className="lg:col-span-3">
            {/* Logo */}
            <div className="flex items-center mb-6">
              <img
                src={logo}
                alt="Black Sky Enterprise"
                className="w-auto"
                style={{ height: "88px" }}
              />
            </div>

            <p
              style={{
                fontFamily: "'Barlow', sans-serif",
                fontWeight: 300,
                fontSize: "0.85rem",
                lineHeight: 1.8,
                color: "rgba(255,255,255,0.35)",
                marginBottom: "24px",
                maxWidth: "260px",
              }}
            >
              Southeast Asia's premier concert promoter and entertainment media company. Creating unforgettable live experiences since 2015.
            </p>

            {/* Contact info */}
            <div className="flex flex-col gap-3 mb-8">
              {[
                { Icon: MapPin, text: "Kuala Lumpur, Malaysia" },
                { Icon: Mail, text: "hello@blackskyenterprise.com" },
                { Icon: Phone, text: "+60 3-XXXX XXXX" },
              ].map(({ Icon, text }, i) => (
                <div key={i} className="flex items-center gap-3">
                  <Icon size={13} style={{ color: "rgba(168,85,247,0.6)", flexShrink: 0 }} />
                  <span
                    style={{
                      fontFamily: "'Barlow', sans-serif",
                      fontWeight: 400,
                      fontSize: "0.78rem",
                      color: "rgba(255,255,255,0.3)",
                    }}
                  >
                    {text}
                  </span>
                </div>
              ))}
            </div>

            {/* Socials */}
            <div className="flex items-center gap-2">
              {socials.map(({ Icon, label, color }, i) => (
                <motion.button
                  key={i}
                  whileHover={{ scale: 1.1, borderColor: color }}
                  className="flex items-center justify-center transition-all duration-300"
                  title={label}
                  style={{
                    width: 38,
                    height: 38,
                    border: "1px solid rgba(255,255,255,0.1)",
                    background: "rgba(255,255,255,0.03)",
                    cursor: "pointer",
                    color: "rgba(255,255,255,0.4)",
                  }}
                >
                  <Icon size={15} />
                </motion.button>
              ))}
            </div>
          </div>

          {/* Spacer */}
          <div className="hidden lg:block lg:col-span-1" />

          {/* Link columns */}
          {Object.entries(footerLinks).map(([title, links]) => (
            <div key={title} className="lg:col-span-2">
              <h4
                style={{
                  fontFamily: "'Barlow Condensed', sans-serif",
                  fontWeight: 700,
                  fontSize: "11px",
                  letterSpacing: "0.3em",
                  color: "rgba(255,255,255,0.5)",
                  marginBottom: "20px",
                  textTransform: "uppercase",
                }}
              >
                {title}
              </h4>
              <ul className="flex flex-col gap-3">
                {links.map((link, i) => (
                  <li key={i}>
                    <button
                      className="flex items-center gap-1 group/link"
                      style={{
                        background: "none",
                        border: "none",
                        cursor: "pointer",
                        padding: 0,
                      }}
                    >
                      <span
                        style={{
                          fontFamily: "'Barlow', sans-serif",
                          fontWeight: 400,
                          fontSize: "0.85rem",
                          color: "rgba(255,255,255,0.3)",
                          transition: "color 0.2s",
                        }}
                        onMouseEnter={(e) =>
                          ((e.currentTarget as HTMLElement).style.color = "rgba(255,255,255,0.8)")
                        }
                        onMouseLeave={(e) =>
                          ((e.currentTarget as HTMLElement).style.color = "rgba(255,255,255,0.3)")
                        }
                      >
                        {link}
                      </span>
                    </button>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>

        {/* Bottom bar */}
        <div
          className="relative z-10 flex flex-col md:flex-row items-center justify-between gap-4 mt-20 pt-8"
          style={{
            borderTop: "1px solid rgba(255,255,255,0.14)",
            background: "rgba(255,255,255,0.025)",
            paddingLeft: "16px",
            paddingRight: "16px",
            paddingBottom: "18px",
          }}
        >
          <span
            style={{
              fontFamily: "'Barlow Condensed', sans-serif",
              fontWeight: 700,
              fontSize: "12px",
              letterSpacing: "0.2em",
              color: "rgba(248,250,252,0.88)",
            }}
          >
            © 2026 BLACK SKY ENTERPRISE SDN. BHD. — ALL RIGHTS RESERVED
          </span>

          <div className="flex items-center gap-6">
            {["Privacy Policy", "Terms of Service", "Cookie Policy"].map((item, i) => (
              <button
                key={i}
                style={{
                  fontFamily: "'Barlow Condensed', sans-serif",
                  fontWeight: 700,
                  fontSize: "12px",
                  letterSpacing: "0.2em",
                  color: "rgba(248,250,252,0.82)",
                  background: "none",
                  border: "none",
                  cursor: "pointer",
                  transition: "color 0.2s",
                }}
                onMouseEnter={(e) =>
                  ((e.currentTarget as HTMLElement).style.color = "rgba(255,255,255,1)")
                }
                onMouseLeave={(e) =>
                  ((e.currentTarget as HTMLElement).style.color = "rgba(248,250,252,0.82)")
                }
              >
                {item}
              </button>
            ))}
          </div>

          <div className="flex items-center gap-2">
            <div
              style={{
                width: 6,
                height: 6,
                borderRadius: "50%",
                background: "#10B981",
                boxShadow: "0 0 6px #10B981",
              }}
            />
            <span
              style={{
                fontFamily: "'Barlow Condensed', sans-serif",
                fontWeight: 700,
                fontSize: "11px",
                letterSpacing: "0.3em",
                color: "rgba(209,250,229,0.92)",
              }}
            >
              MALAYSIA
            </span>
          </div>
        </div>
      </div>
    </footer>
  );
}
