import { useEffect, useMemo } from "react";
import { useQuery } from "@tanstack/react-query";
import { format, parseISO } from "date-fns";
import {
  ArrowLeft,
  ArrowUpRight,
  CalendarDays,
  Clock3,
  Disc3,
  ExternalLink,
  ListMusic,
  MapPinned,
  MapPin,
  Music2,
  Ticket,
  UserRound,
} from "lucide-react";
import { Link, useParams } from "react-router";
import { Footer } from "../components/Footer";
import { Navbar } from "../components/Navbar";
import { SaveEventButton } from "../components/SaveEventButton";
import {
  getPublicEvent,
  type PublicEventDetail,
  type PublicEventSection,
} from "../events/events-api";
import "./EventDetailPage.css";

const FALLBACK_IMAGE =
  "https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=1600&q=82";

const SEAT_MAP_FALLBACK_COPY =
  "Seat map and event guide will be updated closer to the event day. Please stay tuned to our page for the latest updates.";

const STATIC_IMPORTANT_INFORMATION =
  "- Ticket purchases, payment confirmation, refunds, and ticket validity are handled by the official ticketing vendor.\n- Entry requirements, venue rules, seating changes, and event updates follow the vendor or venue policy.\n- Please review the vendor checkout page and event terms before completing your purchase.";

type DetailSection = Pick<PublicEventSection, "section_key" | "title" | "content" | "image_url">;

const DETAIL_SECTION_ORDER = [
  "about",
  "event_details",
  "location",
  "seat_map_ticket_pricing",
  "ticket_pricing",
  "important_information",
];

const DETAIL_SECTION_TITLES: Record<string, string> = {
  about: "About",
  event_details: "Event Details",
  location: "Location",
  seat_map_ticket_pricing: "Seat Map & Ticket Pricing",
  ticket_pricing: "Ticket Pricing",
  important_information: "Important Information",
};

function formatDate(value: string | null) {
  if (!value) {
    return "Date announced soon";
  }

  const date = parseISO(value);

  return Number.isNaN(date.getTime()) ? value : format(date, "dd MMMM yyyy");
}

function formatDayLabel(value: string | null) {
  if (!value) {
    return "01";
  }

  const date = parseISO(value);

  return Number.isNaN(date.getTime()) ? "01" : format(date, "dd");
}

function countryName(code: string) {
  try {
    return new Intl.DisplayNames(["en"], { type: "region" }).of(code.toUpperCase()) ?? code;
  } catch {
    return code;
  }
}

function sectionId(section: DetailSection) {
  return `event-${section.section_key.replace(/[^a-z0-9]+/gi, "-").toLowerCase()}`;
}

function mapsHref(event: PublicEventDetail) {
  return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(
    `${event.venue} ${event.city} ${countryName(event.country_code)}`,
  )}`;
}

function safeExternalUrl(value: string | null | undefined) {
  if (!value?.trim()) {
    return null;
  }

  try {
    const url = new URL(value.trim());

    return ["http:", "https:"].includes(url.protocol) ? url.toString() : null;
  } catch {
    return null;
  }
}

function renderContent(content: string | null) {
  const blocks = (content ?? "")
    .split(/\n{2,}/)
    .map((block) => block.trim())
    .filter(Boolean);

  if (blocks.length === 0) {
    return <p>Details will be updated closer to the event date.</p>;
  }

  return blocks.map((block, index) => {
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
  });
}

function buildSections(event: PublicEventDetail): DetailSection[] {
  const userSections = event.sections
    .filter((section) => section.content?.trim() || section.image_url)
    .flatMap((section) => {
      if (section.section_key === "ticket_pricing" && section.image_url) {
        const sections: DetailSection[] = [
          {
            ...section,
            section_key: "seat_map_ticket_pricing",
            title: DETAIL_SECTION_TITLES.seat_map_ticket_pricing,
            content: "",
          },
        ];

        if (section.content?.trim()) {
          sections.push({
            ...section,
            title: DETAIL_SECTION_TITLES.ticket_pricing,
            image_url: null,
          });
        }

        return sections;
      }

      return [
        {
          ...section,
          title: DETAIL_SECTION_TITLES[section.section_key] ?? section.title,
          content:
            section.section_key === "important_information"
              ? STATIC_IMPORTANT_INFORMATION
              : section.content,
        },
      ];
    });
  const sectionsByKey = new Map<string, DetailSection>(
    userSections.map((section) => [section.section_key, section] as [string, DetailSection]),
  );

  if (!sectionsByKey.has("about")) {
    sectionsByKey.set("about", {
      section_key: "about",
      title: "About",
      content:
        event.subtitle ??
        `${event.title} is a Black Sky Enterprise live event curated for fans across Southeast Asia.`,
      image_url: null,
    });
  }

  if (!sectionsByKey.has("event_details")) {
    sectionsByKey.set("event_details", {
      section_key: "event_details",
      title: "Event Details",
      content: "",
      image_url: null,
    });
  }

  if (!sectionsByKey.has("location")) {
    sectionsByKey.set("location", {
      section_key: "location",
      title: "Location",
      content: "",
      image_url: null,
    });
  }

  if (!sectionsByKey.has("seat_map_ticket_pricing")) {
    sectionsByKey.set("seat_map_ticket_pricing", {
      section_key: "seat_map_ticket_pricing",
      title: "Seat Map & Ticket Pricing",
      content: "",
      image_url: null,
    });
  }

  if (!sectionsByKey.has("ticket_pricing")) {
    sectionsByKey.set("ticket_pricing", {
      section_key: "ticket_pricing",
      title: "Ticket Pricing",
      content: "",
      image_url: null,
    });
  }

  if (!sectionsByKey.has("important_information")) {
    sectionsByKey.set("important_information", {
      section_key: "important_information",
      title: "Important Information",
      content: STATIC_IMPORTANT_INFORMATION,
      image_url: null,
    });
  }

  const orderedSections = DETAIL_SECTION_ORDER
    .map((key) => sectionsByKey.get(key))
    .filter((section): section is DetailSection => Boolean(section));
  const extraSections = userSections.filter((section) => !DETAIL_SECTION_ORDER.includes(section.section_key));

  return [...orderedSections, ...extraSections];
}

function EventSpotifyPreview({ event }: { event: PublicEventDetail }) {
  return (
    <article className="event-detail-preview-card">
      <div className="event-detail-card-heading">
        <span>Music Preview</span>
        <strong>{event.spotify_embed_url ? "Spotify" : event.genre}</strong>
      </div>

      {event.spotify_embed_url ? (
        <iframe
          src={event.spotify_embed_url}
          title={`${event.title} Spotify preview`}
          loading="lazy"
          allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
        />
      ) : (
        <div className="event-detail-preview-card__fallback">
          <img src={event.image_url ?? FALLBACK_IMAGE} alt="" aria-hidden="true" />
          <div>
            <Disc3 aria-hidden="true" />
            <span>Artist preview</span>
            <strong>{event.title}</strong>
            <p>Music preview will be announced closer to show day.</p>
          </div>
        </div>
      )}
    </article>
  );
}

function EventDetailsBlock({ event }: { event: PublicEventDetail }) {
  return (
    <div className="event-detail-reference-card">
      <div className="event-detail-reference-venue">
        <span>Venue</span>
        <a href={mapsHref(event)} target="_blank" rel="noreferrer">
          {event.venue}
          <ExternalLink aria-hidden="true" />
        </a>
      </div>

      <div className="event-detail-reference-day">
        <div>
          <span>Day</span>
          <strong>{formatDayLabel(event.start_date)}</strong>
        </div>
        <div>
          <strong>{formatDate(event.start_date)}</strong>
          <span>{event.time ?? "Time announced soon"}</span>
        </div>
      </div>
    </div>
  );
}

function EventLocationBlock({ event, mapsUrl }: { event: PublicEventDetail; mapsUrl?: string | null }) {
  const href = safeExternalUrl(mapsUrl) ?? mapsHref(event);

  return (
    <div className="event-detail-location-card">
      <MapPinned aria-hidden="true" />
      <div>
        <span>Location</span>
        <strong>{event.venue}</strong>
        <p>
          {event.city}, {countryName(event.country_code)}
        </p>
      </div>
      <a href={href} target="_blank" rel="noreferrer">
        Get Directions
        <ExternalLink aria-hidden="true" />
      </a>
    </div>
  );
}

function EventSectionContent({
  event,
  section,
}: {
  event: PublicEventDetail;
  section: DetailSection;
}) {
  const hasContent = Boolean(section.content?.trim());

  if (section.section_key === "event_details") {
    return (
      <>
        <EventDetailsBlock event={event} />
        {hasContent ? <div className="event-detail-section__rich-text">{renderContent(section.content)}</div> : null}
      </>
    );
  }

  if (section.section_key === "location") {
    return (
      <EventLocationBlock event={event} mapsUrl={section.content} />
    );
  }

  if (section.section_key === "seat_map_ticket_pricing") {
    return (
      <div className="event-detail-ticketing-copy">
        <ListMusic aria-hidden="true" />
        <div>
          {section.image_url ? (
            <img className="event-detail-seat-map" src={section.image_url} alt={`${event.title} seat map`} />
          ) : (
            <p className="event-detail-seat-map-fallback">{SEAT_MAP_FALLBACK_COPY}</p>
          )}
        </div>
      </div>
    );
  }

  if (section.section_key === "ticket_pricing") {
    return (
      <div className="event-detail-ticketing-copy">
        <ListMusic aria-hidden="true" />
        <div>{hasContent ? renderContent(section.content) : <p>Ticket pricing will be announced closer to ticket launch.</p>}</div>
      </div>
    );
  }

  return <>{renderContent(section.content)}</>;
}

function EventInfoPanel({ event }: { event: PublicEventDetail }) {
  const ticketLabel = event.status === "sold_out" ? "Sold Out" : "Buy Tickets";

  return (
    <aside className="event-detail-panel" aria-label="Event showtime and ticket">
      <div className="event-detail-panel__head">
        <span>Showtime</span>
        <strong>{formatDate(event.start_date)}</strong>
      </div>

      <div className="event-detail-panel__rows">
        <div>
          <CalendarDays aria-hidden="true" />
          <span>{event.date}</span>
        </div>
        <div>
          <Clock3 aria-hidden="true" />
          <span>{event.time ?? "Time announced soon"}</span>
        </div>
        <div>
          <MapPin aria-hidden="true" />
          <span>
            {event.venue}, {event.city}
          </span>
        </div>
      </div>

      <div className="event-detail-panel__actions">
        <SaveEventButton
          eventId={event.id}
          idleLabel="Save Event"
          savedLabel="Saved Event"
          loginLabel="Save Event"
        />
        {event.vendor_url && event.status !== "sold_out" ? (
          <a className="event-detail-ticket" href={event.vendor_url} target="_blank" rel="noreferrer">
            <Ticket aria-hidden="true" />
            {ticketLabel}
            <ArrowUpRight aria-hidden="true" />
          </a>
        ) : (
          <button className="event-detail-ticket" type="button" disabled>
            <Ticket aria-hidden="true" />
            {ticketLabel}
          </button>
        )}
      </div>
    </aside>
  );
}

export function EventDetailPage() {
  const { slug = "" } = useParams();
  const eventQuery = useQuery({
    queryKey: ["public-event", slug],
    queryFn: () => getPublicEvent(slug),
    enabled: Boolean(slug),
    retry: false,
  });
  const event = eventQuery.data;
  const sections = useMemo(() => (event ? buildSections(event) : []), [event]);

  useEffect(() => {
    if (!event) {
      return;
    }

    document.title = event.meta_title ?? `${event.title} | Black Sky Enterprise`;
  }, [event]);

  useEffect(() => {
    if (!event || !window.location.hash) {
      return;
    }

    const frame = window.requestAnimationFrame(() => {
      document.querySelector(window.location.hash)?.scrollIntoView({ block: "start" });
    });

    return () => window.cancelAnimationFrame(frame);
  }, [event, sections.length]);

  return (
    <>
      <Navbar />
      <main className="event-detail-page">
        {eventQuery.isLoading ? (
          <section className="event-detail-empty">
            <Clock3 aria-hidden="true" />
            <strong>Loading event</strong>
          </section>
        ) : eventQuery.isError || !event ? (
          <section className="event-detail-empty">
            <Ticket aria-hidden="true" />
            <strong>Event not found</strong>
            <Link to="/discover">Back to Discover</Link>
          </section>
        ) : (
          <>
            <section className="event-detail-hero">
              <img
                className="event-detail-hero__bg"
                src={event.image_url ?? FALLBACK_IMAGE}
                alt=""
                aria-hidden="true"
              />
              <div className="event-detail-hero__shade" />

              <div className="event-detail-hero__inner">
                <div className="event-detail-hero__copy">
                  <Link className="event-detail-back" to="/discover">
                    <ArrowLeft aria-hidden="true" />
                    Discover
                  </Link>

                  <div className="event-detail-kicker">
                    <span>{event.genre}</span>
                    <span>{event.status === "sold_out" ? "Sold Out" : "Live Event"}</span>
                  </div>
                  <h1>{event.title}</h1>
                  <p>{event.subtitle ?? "A Black Sky Enterprise live entertainment experience."}</p>
                  <div className="event-detail-meta">
                    <span>
                      <CalendarDays aria-hidden="true" />
                      {event.date}
                    </span>
                    <span>
                      <MapPin aria-hidden="true" />
                      {event.venue}, {event.city}
                    </span>
                    <span>
                      <Music2 aria-hidden="true" />
                      {event.genre}
                    </span>
                  </div>
                </div>

                <div className="event-detail-poster-card">
                  <img src={event.image_url ?? FALLBACK_IMAGE} alt={`${event.title} artwork`} />
                  <div>
                    <span>Organizer</span>
                    {event.organizer_url ? (
                      <a href={event.organizer_url} target="_blank" rel="noreferrer">
                        {event.organizer_name}
                      </a>
                    ) : (
                      <strong>{event.organizer_name}</strong>
                    )}
                  </div>
                </div>
              </div>
            </section>

            <section className="event-detail-body">
              <div className="event-detail-layout">
                <div className="event-detail-main">
                  <nav className="event-detail-section-nav" aria-label="Event detail sections">
                    {sections.map((section) => (
                      <a key={section.section_key + section.title} href={`#${sectionId(section)}`}>
                        {section.title}
                      </a>
                    ))}
                  </nav>

                  {sections.map((section) => (
                    <details
                      key={section.section_key + section.title}
                      id={sectionId(section)}
                      className="event-detail-section"
                      open
                    >
                      <summary>{section.title}</summary>
                      <div className="event-detail-section__content">
                        <EventSectionContent event={event} section={section} />
                      </div>
                    </details>
                  ))}
                </div>

                <div className="event-detail-aside">
                  <EventSpotifyPreview event={event} />
                  <EventInfoPanel event={event} />
                  <div className="event-detail-organizer">
                    <UserRound aria-hidden="true" />
                    <div>
                      <span>Presented by</span>
                      <strong>{event.organizer_name}</strong>
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </>
        )}
      </main>
      <Footer />
    </>
  );
}
