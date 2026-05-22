import * as React from "react";
import { renderToStaticMarkup } from "react-dom/server";
import { CircleFlag } from "react-circle-flags";
import { countries } from "country-data-list";

type Country = {
  alpha2: string;
  alpha3: string;
  name: string;
  status: string;
};

type DashboardCountryFlagProps = {
  code: string;
  label: string;
};

const countryOptions = countries.all
  .filter((country: Country) => country.alpha2 && country.alpha3 && country.status === "assigned")
  .sort((first: Country, second: Country) => first.name.localeCompare(second.name));

function findCountry(value: string): Country | undefined {
  const normalized = value.trim().toUpperCase();

  if (!normalized) {
    return undefined;
  }

  return countryOptions.find(
    (country: Country) => country.alpha2 === normalized || country.alpha3 === normalized,
  );
}

function DashboardCountryFlag({ code, label }: DashboardCountryFlagProps) {
  const country = findCountry(code);

  if (!country) {
    return <span className="bsa-country-flag__fallback">{code.toUpperCase()}</span>;
  }

  return (
    <CircleFlag
      aria-label={label}
      countryCode={country.alpha2.toLowerCase()}
      height={28}
    />
  );
}

export function mountDashboardCountryFlags(): void {
  document.querySelectorAll<HTMLElement>("[data-bsa-country-code]").forEach((element) => {
    const code = element.dataset.bsaCountryCode ?? "";
    const label = element.getAttribute("aria-label") ?? `${code.toUpperCase()} flag`;
    const country = findCountry(code);
    const renderKey = country ? `${country.alpha2}:${label}` : `fallback:${code.toUpperCase()}`;

    if (element.dataset.bsaRenderedFlag === renderKey) {
      return;
    }

    element.innerHTML = renderToStaticMarkup(<DashboardCountryFlag code={code} label={label} />);
    element.dataset.bsaRenderedFlag = renderKey;
  });
}
