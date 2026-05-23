import { useEffect, useMemo, useRef, useState } from "react";
import { useInfiniteQuery } from "@tanstack/react-query";
import { format, parseISO } from "date-fns";
import flatpickr from "flatpickr";
import type { Instance } from "flatpickr/dist/types/instance";
import {
  ArrowUpRight,
  CalendarDays,
  ChevronLeft,
  ChevronRight,
  MapPin,
  Music2,
  Search,
  SlidersHorizontal,
  Ticket,
  X,
} from "lucide-react";
import { FilterDropdown } from "../components/FilterDropdown";
import { Footer } from "../components/Footer";
import { Navbar } from "../components/Navbar";
import { SaveEventButton } from "../components/SaveEventButton";
import {
  getPublicEvents,
  type PublicEvent,
  type PublicEventQuery,
} from "../events/events-api";
import "flatpickr/dist/flatpickr.css";
import "./DiscoverPage.css";

type Timeframe = "upcoming" | "past";
type Availability = "all" | "available" | "sold_out";

const FALLBACK_IMAGE =
  "https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=1400&q=80";

function useDebouncedValue(value: string, delay = 350) {
  const [debouncedValue, setDebouncedValue] = useState(value);

  useEffect(() => {
    const timeout = window.setTimeout(() => setDebouncedValue(value), delay);

    return () => window.clearTimeout(timeout);
  }, [delay, value]);

  return debouncedValue;
}

function toDate(value: string) {
  const date = parseISO(value);

  return Number.isNaN(date.getTime()) ? null : date;
}

function formatMonth(value: string) {
  const date = toDate(value);

  return date ? format(date, "MMMM yyyy").toUpperCase() : "UPCOMING";
}

function formatDay(value: string) {
  const date = toDate(value);

  return {
    weekday: date ? format(date, "EEE").toUpperCase() : "TBA",
    day: date ? format(date, "dd") : "--",
  };
}

function eventCta() {
  return "View Details";
}

function eventHref(event: PublicEvent) {
  return `/events/${encodeURIComponent(event.slug)}`;
}

function groupEventsByMonth(events: PublicEvent[]) {
  return events.reduce<Record<string, PublicEvent[]>>((groups, event) => {
    const month = formatMonth(event.start_date);
    groups[month] = groups[month] ?? [];
    groups[month].push(event);

    return groups;
  }, {});
}

function DiscoverDateField({
  label,
  value,
  onChange,
}: {
  label: string;
  value: string;
  onChange: (value: string) => void;
}) {
  const inputRef = useRef<HTMLInputElement | null>(null);
  const pickerRef = useRef<Instance | null>(null);
  const onChangeRef = useRef(onChange);

  useEffect(() => {
    onChangeRef.current = onChange;
  }, [onChange]);

  useEffect(() => {
    if (!inputRef.current) {
      return undefined;
    }

    const picker = flatpickr(inputRef.current, {
      allowInput: false,
      altFormat: "M j, Y",
      altInput: true,
      altInputClass: "discover-date-input",
      dateFormat: "Y-m-d",
      defaultDate: value || undefined,
      disableMobile: true,
      onChange: (_selectedDates, dateString) => {
        onChangeRef.current(dateString);
      },
      onReady: (_selectedDates, _dateString, instance) => {
        instance.calendarContainer.classList.add("discover-flatpickr-calendar");
      },
    });

    pickerRef.current = Array.isArray(picker) ? picker[0] ?? null : picker;

    return () => {
      pickerRef.current?.destroy();
      pickerRef.current = null;
    };
  }, []);

  useEffect(() => {
    pickerRef.current?.setDate(value || "", false);
  }, [value]);

  return (
    <label className="discover-date-field">
      <CalendarDays aria-hidden="true" />
      <span>{label}</span>
      <input ref={inputRef} type="text" aria-label={`${label} date`} placeholder="Select date" />
    </label>
  );
}

function HeroShowcase({ events }: { events: PublicEvent[] }) {
  const [activeIndex, setActiveIndex] = useState(0);
  const heroEvents = events.slice(0, 5);
  const activeEvent = heroEvents[activeIndex] ?? heroEvents[0];

  useEffect(() => {
    setActiveIndex(0);
  }, [events]);

  if (!activeEvent) {
    return (
      <section className="discover-hero discover-hero-empty" aria-label="Featured events">
        <div className="discover-hero-copy">
          <div className="discover-hero-kicker">
            <div aria-hidden="true" />
            <span>Discover</span>
          </div>
          <h1>Find the next Black Sky show</h1>
          <p>Explore upcoming concerts, festivals, and live entertainment across Southeast Asia.</p>
        </div>
      </section>
    );
  }

  const image = activeEvent.image_url ?? FALLBACK_IMAGE;

  return (
    <section className="discover-hero" aria-label="Featured events">
      <img src={image} alt="" className="discover-hero-bg" aria-hidden="true" />
      <div className="discover-hero-shade" />
      <div className="discover-hero-inner">
        <div className="discover-hero-copy">
          <div className="discover-hero-kicker">
            <div aria-hidden="true" />
            <span>Featured Event</span>
          </div>
          <h1>{activeEvent.title}</h1>
          <p>{activeEvent.subtitle ?? "A Black Sky live experience coming to the region."}</p>
          <div className="discover-hero-meta">
            <span>
              <CalendarDays aria-hidden="true" />
              {activeEvent.date}
            </span>
            <span>
              <MapPin aria-hidden="true" />
              {activeEvent.venue}, {activeEvent.city}
            </span>
          </div>
        </div>

        <div className="discover-hero-panel">
          <img src={image} alt={`${activeEvent.title} artwork`} />
          <div className="discover-hero-panel__footer">
            <div className="discover-hero-panel__meta-line">
              <span>{activeEvent.genre}</span>
              <strong>{activeEvent.status === "sold_out" ? "Sold Out" : "Featured"}</strong>
            </div>
            <div className="discover-hero-panel__actions">
              <SaveEventButton
                eventId={activeEvent.id}
                compact
                idleLabel="Save"
                savedLabel="Saved"
                loginLabel="Save"
              />
              <a href={eventHref(activeEvent)}>
                <span>{eventCta()}</span>
                <ArrowUpRight aria-hidden="true" />
              </a>
            </div>
          </div>
        </div>
      </div>

      {heroEvents.length > 1 && (
        <div className="discover-hero-controls" aria-label="Featured event controls">
          <button
            type="button"
            aria-label="Previous featured event"
            onClick={() =>
              setActiveIndex((index) => (index === 0 ? heroEvents.length - 1 : index - 1))
            }
          >
            <ChevronLeft aria-hidden="true" />
          </button>
          <div>
            {heroEvents.map((event, index) => (
              <button
                key={event.id}
                type="button"
                aria-label={`Show ${event.title}`}
                aria-current={index === activeIndex}
                onClick={() => setActiveIndex(index)}
              />
            ))}
          </div>
          <button
            type="button"
            aria-label="Next featured event"
            onClick={() =>
              setActiveIndex((index) => (index === heroEvents.length - 1 ? 0 : index + 1))
            }
          >
            <ChevronRight aria-hidden="true" />
          </button>
        </div>
      )}
    </section>
  );
}

function DiscoverEventRow({ event }: { event: PublicEvent }) {
  const day = formatDay(event.start_date);
  const image = event.image_url ?? FALLBACK_IMAGE;

  return (
    <article className="discover-event-row">
      <div className="discover-date-badge" aria-label={event.date}>
        <span>{day.weekday}</span>
        <strong>{day.day}</strong>
      </div>

      <img src={image} alt={`${event.title} event artwork`} loading="lazy" />

      <div className="discover-event-main">
        <div className="discover-event-kicker">
          <span>{event.genre}</span>
          <span>{event.status === "sold_out" ? "Sold out" : "Upcoming"}</span>
        </div>
        <h3>{event.title}</h3>
        <p>{event.subtitle ?? "Black Sky curated live entertainment experience."}</p>
        <div className="discover-event-meta">
          <span>
            <CalendarDays aria-hidden="true" />
            {event.date}
          </span>
          <span>
            <MapPin aria-hidden="true" />
            {event.venue}, {event.city}
          </span>
        </div>
      </div>

      <div className="discover-event-side">
        <span className="discover-ticket-label">
          <Ticket aria-hidden="true" />
          Event details
        </span>
        <SaveEventButton
          eventId={event.id}
          compact
          idleLabel="Save"
          savedLabel="Saved"
          loginLabel="Save"
        />
        <a
          href={eventHref(event)}
        >
          <span>{eventCta()}</span>
          <ArrowUpRight aria-hidden="true" />
        </a>
      </div>
    </article>
  );
}

export function DiscoverPage() {
  const [timeframe, setTimeframe] = useState<Timeframe>("upcoming");
  const [search, setSearch] = useState("");
  const [city, setCity] = useState("");
  const [genre, setGenre] = useState("");
  const [dateFrom, setDateFrom] = useState("");
  const [dateTo, setDateTo] = useState("");
  const [availability, setAvailability] = useState<Availability>("all");
  const debouncedSearch = useDebouncedValue(search);

  useEffect(() => {
    document.title = "Discover Events | Black Sky Enterprise";

    const description =
      "Explore Black Sky Enterprise concerts, festivals, and live entertainment events across Malaysia, Indonesia, and Southeast Asia.";
    const metaDescription = document.querySelector<HTMLMetaElement>("meta[name='description']");
    const canonical = document.querySelector<HTMLLinkElement>("link[rel='canonical']");

    metaDescription?.setAttribute("content", description);
    canonical?.setAttribute("href", `${window.location.origin}/discover`);
  }, []);

  const query: PublicEventQuery = {
    search: debouncedSearch,
    city,
    genre,
    dateFrom,
    dateTo,
    timeframe,
    availability,
    perPage: 24,
  };

  const eventsQuery = useInfiniteQuery({
    queryKey: ["public-events", query],
    queryFn: ({ pageParam }) =>
      getPublicEvents({
        ...query,
        cursor: pageParam,
      }),
    initialPageParam: "",
    getNextPageParam: (lastPage) => lastPage.meta.next_cursor ?? undefined,
  });

  const pages = eventsQuery.data?.pages ?? [];
  const events = pages.flatMap((page) => page.data);
  const filterOptions = pages[0]?.meta.filters ?? { cities: [], genres: [] };
  const cityOptions = useMemo(
    () => [
      { value: "", label: "All Cities" },
      ...filterOptions.cities.map((cityOption) => ({
        value: cityOption,
        label: cityOption,
      })),
    ],
    [filterOptions.cities],
  );
  const genreOptions = useMemo(
    () => [
      { value: "", label: "All Genres" },
      ...filterOptions.genres.map((genreOption) => ({
        value: genreOption,
        label: genreOption,
      })),
    ],
    [filterOptions.genres],
  );
  const groupedEvents = useMemo(() => groupEventsByMonth(events), [events]);
  const hasActiveFilters = Boolean(search || city || genre || dateFrom || dateTo || availability !== "all");

  const clearFilters = () => {
    setSearch("");
    setCity("");
    setGenre("");
    setDateFrom("");
    setDateTo("");
    setAvailability("all");
  };

  return (
    <>
      <Navbar />
      <main className="discover-page">
        <HeroShowcase events={events} />

        <section className="discover-list-section" id="events-list">
          <div className="discover-browsing">
            <span>Currently browsing</span>
            <div aria-label="Event timeframe">
              <button
                type="button"
                aria-pressed={timeframe === "upcoming"}
                onClick={() => setTimeframe("upcoming")}
              >
                Upcoming Events
              </button>
              <button
                type="button"
                aria-pressed={timeframe === "past"}
                onClick={() => setTimeframe("past")}
              >
                Past Events
              </button>
            </div>
          </div>

          <div className="discover-heading-row">
            <div>
              <span>Discover</span>
              <h2>
                All concerts and events
                <strong>{city ? ` in ${city}` : " in Southeast Asia"}</strong>
              </h2>
            </div>
            <p>
              Browse Black Sky events by city, date, genre, and availability. Results are loaded with
              cursor pagination for a fast public discovery page.
            </p>
          </div>

          <div className="discover-filter-bar" aria-label="Discover filters">
            <label className="discover-search-field">
              <Search aria-hidden="true" />
              <span className="sr-only">Search by artist or event</span>
              <input
                type="search"
                value={search}
                onChange={(event) => setSearch(event.target.value)}
                placeholder="Search by artist or event"
              />
            </label>

            <FilterDropdown
              ariaLabel="City"
              value={city}
              options={cityOptions}
              onChange={setCity}
              icon={<MapPin aria-hidden="true" />}
            />

            <FilterDropdown
              ariaLabel="Event genre"
              value={genre}
              options={genreOptions}
              onChange={setGenre}
              icon={<Music2 aria-hidden="true" />}
            />

            <DiscoverDateField label="From" value={dateFrom} onChange={setDateFrom} />

            <DiscoverDateField label="To" value={dateTo} onChange={setDateTo} />

            <FilterDropdown
              ariaLabel="Availability"
              value={availability}
              options={[
                { value: "all", label: "All Status" },
                { value: "available", label: "Available" },
                { value: "sold_out", label: "Sold Out" },
              ]}
              onChange={(nextValue) => setAvailability(nextValue as Availability)}
              icon={<SlidersHorizontal aria-hidden="true" />}
            />

            <button type="button" onClick={clearFilters} disabled={!hasActiveFilters}>
              <X aria-hidden="true" />
              Clear
            </button>
          </div>

          <div className="discover-results" aria-live="polite">
            {eventsQuery.isLoading ? (
              <div className="discover-empty">
                <CalendarDays aria-hidden="true" />
                <strong>Loading events</strong>
                <span>Preparing the latest published event catalog.</span>
              </div>
            ) : eventsQuery.isError ? (
              <div className="discover-empty">
                <CalendarDays aria-hidden="true" />
                <strong>Events could not load</strong>
                <span>Please refresh the page or try again shortly.</span>
              </div>
            ) : events.length === 0 ? (
              <div className="discover-empty">
                <CalendarDays aria-hidden="true" />
                <strong>No events found</strong>
                <span>Try clearing filters or checking a different timeframe.</span>
              </div>
            ) : (
              Object.entries(groupedEvents).map(([month, monthEvents]) => (
                <section key={month} className="discover-month-group" aria-labelledby={`discover-${month}`}>
                  <h2 id={`discover-${month}`}>{month}</h2>
                  <div>
                    {monthEvents.map((event) => (
                      <DiscoverEventRow key={event.id} event={event} />
                    ))}
                  </div>
                </section>
              ))
            )}
          </div>

          {eventsQuery.hasNextPage && (
            <div className="discover-load-more">
              <button
                type="button"
                onClick={() => eventsQuery.fetchNextPage()}
                disabled={eventsQuery.isFetchingNextPage}
              >
                {eventsQuery.isFetchingNextPage ? "Loading" : "Load More"}
              </button>
            </div>
          )}
        </section>
      </main>
      <Footer />
    </>
  );
}
