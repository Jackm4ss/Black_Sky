import { useState, type MouseEvent } from "react";
import { motion, AnimatePresence } from "motion/react";
import { ChevronDown, LayoutDashboard, LogIn, LogOut, Menu, UserRound, X } from "lucide-react";
import { isAuthUserEmailVerified } from "../auth/auth-api";
import { useCurrentUser, useLogoutMutation } from "../auth/auth-queries";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "./ui/dropdown-menu";
import logo from "../../assets/LOGO.png";

const navLinks = [
  { label: "ABOUT", href: "/#about" },
  { label: "DISCOVER", href: "/discover" },
  { label: "WORKS", href: "/#works" },
  { label: "NEWS", href: "/news" },
  { label: "CONTACT", href: "/#contact" },
];

export function Navbar() {
  const [mobileOpen, setMobileOpen] = useState(false);
  const { data: user } = useCurrentUser();
  const logoutMutation = useLogoutMutation();
  const isAdmin = user?.roles?.includes("admin") && !user.roles.includes("user");
  const isVerifiedMember = Boolean(user?.roles?.includes("user") && isAuthUserEmailVerified(user));
  const accountDisplayName = user?.name?.trim() || user?.email || "Account";
  const accountHref = user ? (isAdmin ? "/admin" : isVerifiedMember ? "/dashboard" : "/email-verification") : "/login";
  const accountMenuLabel = isAdmin || isVerifiedMember ? "Dashboard" : "Verify Email";
  const accountLabel = user ? accountDisplayName : "LOGIN";
  const showTicketCta = !user;

  const handleLogout = async () => {
    setMobileOpen(false);
    await logoutMutation.mutateAsync();
    window.location.assign("/login");
  };

  const handleLinkClick = (event: MouseEvent<HTMLAnchorElement>, href: string) => {
    setMobileOpen(false);

    if (
      event.defaultPrevented ||
      event.button !== 0 ||
      event.metaKey ||
      event.ctrlKey ||
      event.shiftKey ||
      event.altKey
    ) {
      return;
    }

    if (href.startsWith("/#") && window.location.pathname === "/") {
      event.preventDefault();
      const el = document.querySelector(href.replace("/", ""));
      if (el) el.scrollIntoView({ behavior: "smooth" });
    }
  };

  return (
    <>
      <motion.nav
        initial={{ y: -80, opacity: 0 }}
        animate={{ y: 0, opacity: 1 }}
        transition={{ duration: 0.8, ease: [0.22, 1, 0.36, 1] }}
        className="fixed top-0 left-0 right-0 z-50 transition-all duration-500"
        style={{
          background: "linear-gradient(135deg, rgba(7,18,33,0.62), rgba(4,8,16,0.46))",
          backdropFilter: "blur(24px) saturate(160%)",
          WebkitBackdropFilter: "blur(24px) saturate(160%)",
          borderBottom: "1px solid rgba(255,255,255,0.14)",
          boxShadow: "0 18px 50px rgba(0,0,0,0.34), inset 0 1px 0 rgba(255,255,255,0.08)",
        }}
      >
        <div className="max-w-[1600px] mx-auto px-8 py-5 flex items-center justify-between">
          {/* Logo */}
          <a
            href="/"
            onClick={(event) => {
              setMobileOpen(false);

              if (
                event.defaultPrevented ||
                event.button !== 0 ||
                event.metaKey ||
                event.ctrlKey ||
                event.shiftKey ||
                event.altKey ||
                window.location.pathname !== "/"
              ) {
                return;
              }

              event.preventDefault();
              window.scrollTo({ top: 0, behavior: "smooth" });
            }}
            className="flex items-center group"
            aria-label="Black Sky Enterprise"
          >
            <img
              src={logo}
              alt="Black Sky Enterprise"
              className="w-auto"
              style={{ height: "clamp(52px, 7vw, 72px)" }}
            />
          </a>

          {/* Desktop Nav */}
          <div className="hidden lg:flex items-center gap-10">
            {navLinks.map((link) => (
              <a
                key={link.label}
                href={link.href}
                onClick={(event) => handleLinkClick(event, link.href)}
                className="relative group"
                style={{
                  fontFamily: "'Barlow Condensed', sans-serif",
                  fontWeight: 600,
                  fontSize: "13px",
                  letterSpacing: "0.2em",
                  color: "rgba(255,255,255,0.65)",
                  background: "none",
                  border: "none",
                  cursor: "pointer",
                  textDecoration: "none",
                  transition: "color 0.3s",
                }}
                onMouseEnter={(e) =>
                  (e.currentTarget.style.color = "rgba(255,255,255,1)")
                }
                onMouseLeave={(e) =>
                  (e.currentTarget.style.color = "rgba(255,255,255,0.65)")
                }
              >
                {link.label}
                <span
                  className="absolute bottom-[-4px] left-0 w-0 group-hover:w-full transition-all duration-300"
                  style={{ height: "1px", background: "#A855F7" }}
                />
              </a>
            ))}
          </div>

          {/* CTA + Mobile */}
          <div className="flex items-center gap-4">
            {user ? (
              <DropdownMenu>
                <DropdownMenuTrigger asChild>
                  <button
                    type="button"
                    className="hidden lg:flex items-center gap-2 px-4 py-3 transition-all duration-300 hover:opacity-90"
                    style={{
                      background: "#FFFFFF",
                      border: "none",
                      color: "#030213",
                      cursor: "pointer",
                      fontFamily: "'Inter', sans-serif",
                      fontSize: "13px",
                      fontWeight: 800,
                      letterSpacing: "0",
                      lineHeight: 1,
                      maxWidth: "240px",
                      textDecoration: "none",
                      textTransform: "none",
                    }}
                  >
                    <UserRound size={14} strokeWidth={2.6} aria-hidden="true" />
                    <span className="truncate">{accountDisplayName}</span>
                    <ChevronDown size={13} strokeWidth={2.8} aria-hidden="true" />
                  </button>
                </DropdownMenuTrigger>
                <DropdownMenuContent
                  align="end"
                  sideOffset={10}
                  className="min-w-[190px] rounded-none border-white/15 bg-[#050b14] p-2 text-white shadow-2xl"
                >
                  <DropdownMenuItem asChild className="rounded-none px-3 py-2.5 !text-[12px] font-semibold normal-case tracking-normal focus:bg-sky-500/15 focus:text-white">
                    <a href={accountHref} onClick={() => setMobileOpen(false)}>
                      <LayoutDashboard size={14} aria-hidden="true" />
                      {accountMenuLabel}
                    </a>
                  </DropdownMenuItem>
                  <DropdownMenuSeparator className="bg-white/10" />
                  <DropdownMenuItem
                    className="rounded-none px-3 py-2.5 !text-[12px] font-semibold normal-case tracking-normal focus:bg-rose-500/15 focus:text-white"
                    disabled={logoutMutation.isPending}
                    onSelect={(event) => {
                      event.preventDefault();
                      void handleLogout();
                    }}
                  >
                    <LogOut size={14} aria-hidden="true" />
                    {logoutMutation.isPending ? "Logging out" : "Logout"}
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            ) : (
              <a
                href={accountHref}
                onClick={() => setMobileOpen(false)}
                className="hidden lg:flex items-center gap-2 px-4 py-3 transition-all duration-300 hover:opacity-90"
                style={{
                  background: "#FFFFFF",
                  border: "none",
                  color: "#030213",
                  cursor: "pointer",
                  fontFamily: "'Barlow Condensed', sans-serif",
                  fontSize: "11px",
                  fontWeight: 700,
                  letterSpacing: "0.25em",
                  lineHeight: 1,
                  textDecoration: "none",
                  textTransform: "uppercase",
                }}
              >
                <LogIn size={14} strokeWidth={2.6} aria-hidden="true" />
                {accountLabel}
              </a>
            )}
            {showTicketCta ? (
              <a
                href="/register"
                onClick={() => setMobileOpen(false)}
                className="hidden lg:flex items-center gap-2 px-6 py-3 transition-all duration-300 hover:opacity-90"
                style={{
                  background: "#0EA5E9",
                  fontFamily: "'Barlow Condensed', sans-serif",
                  fontWeight: 700,
                  fontSize: "11px",
                  letterSpacing: "0.25em",
                  color: "#fff",
                  border: "none",
                  cursor: "pointer",
                  textDecoration: "none",
                }}
              >
                GET TICKETS
              </a>
            ) : null}
            <button
              className="lg:hidden text-white p-1"
              onClick={() => setMobileOpen(true)}
              style={{ background: "none", border: "none", cursor: "pointer" }}
            >
              <Menu size={24} />
            </button>
          </div>
        </div>
      </motion.nav>

      {/* Mobile Menu */}
      <AnimatePresence>
        {mobileOpen && (
          <motion.div
            initial={{ x: "100%" }}
            animate={{ x: 0 }}
            exit={{ x: "100%" }}
            transition={{ duration: 0.4, ease: [0.22, 1, 0.36, 1] }}
            className="fixed inset-0 z-[100] flex flex-col overflow-y-auto"
            style={{
              background: "#050505",
              height: "100dvh",
              WebkitOverflowScrolling: "touch",
              overscrollBehavior: "contain",
            }}
          >
            <div className="flex shrink-0 items-center justify-between px-8 py-5">
              <img
                src={logo}
                alt="Black Sky Enterprise"
                className="w-auto"
                style={{ height: "64px" }}
              />
              <button
                onClick={() => setMobileOpen(false)}
                style={{
                  background: "none",
                  border: "none",
                  cursor: "pointer",
                  color: "#fff",
                }}
              >
                <X size={28} />
              </button>
            </div>
            <div className="flex min-h-0 flex-1 flex-col justify-center px-8 py-6 gap-2">
              {navLinks.map((link, i) => (
                <motion.a
                  key={link.label}
                  href={link.href}
                  initial={{ x: 60, opacity: 0 }}
                  animate={{ x: 0, opacity: 1 }}
                  transition={{ delay: i * 0.08, duration: 0.4 }}
                  onClick={(event) => handleLinkClick(event, link.href)}
                  className="text-left py-4"
                  style={{
                    fontFamily: "'Barlow Condensed', sans-serif",
                    fontWeight: 800,
                    fontSize: "clamp(2.5rem, 8vw, 4rem)",
                    letterSpacing: "0.05em",
                    color: "rgba(255,255,255,0.9)",
                    background: "none",
                    border: "none",
                    borderBottom: "1px solid rgba(255,255,255,0.08)",
                    cursor: "pointer",
                    textDecoration: "none",
                  }}
                >
                  {link.label}
                </motion.a>
              ))}
            </div>
            <div className="shrink-0 px-8 pb-12">
              <a
                href={accountHref}
                onClick={() => setMobileOpen(false)}
                className="w-full py-4 mb-3"
                style={{
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "center",
                  gap: "10px",
                  background: "#FFFFFF",
                  fontFamily: user ? "'Inter', sans-serif" : "'Barlow Condensed', sans-serif",
                  fontWeight: 700,
                  fontSize: user ? "14px" : "12px",
                  letterSpacing: user ? "0" : "0.25em",
                  color: "#030213",
                  border: "none",
                  cursor: "pointer",
                  textAlign: "center",
                  textDecoration: "none",
                  textTransform: user ? "none" : "uppercase",
                }}
              >
                {user ? (
                  <UserRound size={16} strokeWidth={2.6} aria-hidden="true" />
                ) : (
                  <LogIn size={16} strokeWidth={2.6} aria-hidden="true" />
                )}
                {accountLabel}
              </a>
              {user ? (
                <button
                  type="button"
                  disabled={logoutMutation.isPending}
                  onClick={() => {
                    void handleLogout();
                  }}
                  className="w-full py-4 mb-3"
                  style={{
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                    gap: "10px",
                    background: "rgba(255,255,255,0.08)",
                    fontFamily: "'Barlow Condensed', sans-serif",
                    fontWeight: 700,
                    fontSize: "12px",
                    letterSpacing: "0.25em",
                    color: "#fff",
                    border: "1px solid rgba(255,255,255,0.16)",
                    cursor: logoutMutation.isPending ? "not-allowed" : "pointer",
                    opacity: logoutMutation.isPending ? 0.72 : 1,
                    textAlign: "center",
                    textDecoration: "none",
                    textTransform: "uppercase",
                  }}
                >
                  <LogOut size={16} strokeWidth={2.6} aria-hidden="true" />
                  {logoutMutation.isPending ? "Logging out" : "Logout"}
                </button>
              ) : null}
              {showTicketCta ? (
                <a
                  href="/register"
                  onClick={() => setMobileOpen(false)}
                  className="w-full py-4"
                  style={{
                    display: "block",
                    background: "#0EA5E9",
                    fontFamily: "'Barlow Condensed', sans-serif",
                    fontWeight: 700,
                    fontSize: "12px",
                    letterSpacing: "0.25em",
                    color: "#fff",
                    border: "none",
                    cursor: "pointer",
                    textAlign: "center",
                    textDecoration: "none",
                  }}
                >
                  GET TICKETS
                </a>
              ) : null}
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </>
  );
}
