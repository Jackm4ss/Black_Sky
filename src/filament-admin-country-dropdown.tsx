import * as React from "react";
import { createRoot, type Root } from "react-dom/client";
import { CheckIcon, ChevronDownIcon } from "lucide-react";
import { CircleFlag } from "react-circle-flags";
import { countries } from "country-data-list";

import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from "./app/components/ui/command";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "./app/components/ui/popover";

import "./styles/filament-admin-country-dropdown.css";

type Country = {
  alpha2: string;
  alpha3: string;
  countryCallingCodes: string[];
  currencies: string[];
  emoji?: string;
  ioc: string;
  languages: string[];
  name: string;
  status: string;
};

type CountryDropdownProps = {
  inputId: string;
  value: string;
};

const roots = new WeakMap<Element, Root>();
const countryOptions = countries.all
  .filter((country: Country) => country.alpha2 && country.alpha3 && country.name && country.status === "assigned")
  .sort((first: Country, second: Country) => first.name.localeCompare(second.name));

function syncInput(inputId: string, value: string): void {
  const input = document.getElementById(inputId) as HTMLInputElement | null;

  if (!input || input.value === value) {
    return;
  }

  input.value = value;
  input.dispatchEvent(new Event("input", { bubbles: true }));
  input.dispatchEvent(new Event("change", { bubbles: true }));
}

function findCountry(value: string): Country | undefined {
  const normalized = value.trim().toUpperCase();

  if (!normalized) {
    return undefined;
  }

  return countryOptions.find(
    (country: Country) => country.alpha2 === normalized || country.alpha3 === normalized,
  );
}

function CountryDropdown({ inputId, value }: CountryDropdownProps) {
  const [open, setOpen] = React.useState(false);
  const [selectedCountry, setSelectedCountry] = React.useState<Country | undefined>(() => findCountry(value));

  React.useEffect(() => {
    setSelectedCountry(findCountry(value));
  }, [value]);

  React.useEffect(() => {
    const input = document.getElementById(inputId) as HTMLInputElement | null;

    if (!input) {
      return;
    }

    const handleInputChange = () => {
      setSelectedCountry(findCountry(input.value));
    };

    handleInputChange();
    input.addEventListener("input", handleInputChange);
    input.addEventListener("change", handleInputChange);

    return () => {
      input.removeEventListener("input", handleInputChange);
      input.removeEventListener("change", handleInputChange);
    };
  }, [inputId]);

  const selectCountry = React.useCallback(
    (country: Country) => {
      setSelectedCountry(country);
      syncInput(inputId, country.alpha2);
      setOpen(false);
    },
    [inputId],
  );

  return (
    <Popover open={open} onOpenChange={setOpen}>
      <PopoverTrigger asChild>
        <button
          type="button"
          className="bsa-country-dropdown-trigger"
          data-empty={!selectedCountry}
          aria-label="Select country"
        >
          {selectedCountry ? (
            <span className="bsa-country-dropdown-value">
              <CircleFlag countryCode={selectedCountry.alpha2.toLowerCase()} height={20} />
              <span>{selectedCountry.name}</span>
            </span>
          ) : (
            <span className="bsa-country-dropdown-value bsa-country-dropdown-value-empty">
              <span>Select country</span>
            </span>
          )}
          <ChevronDownIcon aria-hidden="true" />
        </button>
      </PopoverTrigger>
      <PopoverContent className="bsa-country-dropdown-content" align="start">
        <Command className="bsa-country-dropdown-command">
          <CommandList className="bsa-country-dropdown-list">
            <div className="bsa-country-dropdown-search">
              <CommandInput placeholder="Search country..." />
            </div>
            <CommandEmpty className="bsa-country-dropdown-empty">No country found.</CommandEmpty>
            <CommandGroup className="bsa-country-dropdown-group">
              {countryOptions.map((country: Country) => (
                <CommandItem
                  key={country.alpha2}
                  value={`${country.name} ${country.alpha2} ${country.alpha3}`}
                  className="bsa-country-dropdown-item"
                  onSelect={() => selectCountry(country)}
                >
                  <span className="bsa-country-dropdown-item-main">
                    <CircleFlag countryCode={country.alpha2.toLowerCase()} height={20} />
                    <span>{country.name}</span>
                  </span>
                  <CheckIcon
                    aria-hidden="true"
                    className={country.alpha2 === selectedCountry?.alpha2 ? "is-selected" : ""}
                  />
                </CommandItem>
              ))}
            </CommandGroup>
          </CommandList>
        </Command>
      </PopoverContent>
    </Popover>
  );
}

export function mountAdminCountryDropdowns(): void {
  document.querySelectorAll<HTMLElement>("[data-bsa-country-dropdown]").forEach((element) => {
    const inputId = element.dataset.countryInput ?? "";
    const input = document.getElementById(inputId) as HTMLInputElement | null;

    if (!inputId || !input) {
      return;
    }

    let root = roots.get(element);

    if (!root) {
      root = createRoot(element);
      roots.set(element, root);
    }

    root.render(<CountryDropdown inputId={inputId} value={input.value} />);
  });
}
