import { forwardRef, useEffect, useMemo, useState } from "react";
import type { ChangeEvent, InputHTMLAttributes } from "react";
import { Check, ChevronsUpDown, Globe2 } from "lucide-react";
import { countries } from "country-data-list";
import { AsYouType, parsePhoneNumberFromString } from "libphonenumber-js";
import type { CountryCode } from "libphonenumber-js";
import { CircleFlag } from "react-circle-flags";
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from "./command";
import { Popover, PopoverContent, PopoverTrigger } from "./popover";
import { cn } from "./utils";

export type PhoneCountry = {
  alpha2: string;
  alpha3: string;
  countryCallingCodes: string[];
  name: string;
  status: string;
};

type PhoneInputProps = Omit<InputHTMLAttributes<HTMLInputElement>, "onChange"> & {
  defaultCountry?: string;
  onChange?: (event: ChangeEvent<HTMLInputElement>) => void;
  onCountryChange?: (country: PhoneCountry | undefined) => void;
  value?: string;
};

const phoneCountries = countries.all
  .filter(
    (country: PhoneCountry) =>
      country.alpha2 &&
      country.name &&
      country.status === "assigned" &&
      country.countryCallingCodes?.[0],
  )
  .sort((first: PhoneCountry, second: PhoneCountry) =>
    first.name.localeCompare(second.name),
  );

function findCountry(value?: string) {
  const normalized = value?.trim().toUpperCase();

  if (!normalized) return undefined;

  return phoneCountries.find(
    (country: PhoneCountry) =>
      country.alpha2.toUpperCase() === normalized ||
      country.alpha3.toUpperCase() === normalized,
  );
}

function normalizePhoneValue(value: string) {
  const trimmed = value.trim();

  if (!trimmed) return "";
  if (trimmed.startsWith("+")) return trimmed;
  if (trimmed.startsWith("00")) return `+${trimmed.slice(2)}`;

  return `+${trimmed}`;
}

function getCountryFromPhone(value?: string) {
  const normalized = normalizePhoneValue(value ?? "");

  if (!normalized) return undefined;

  const parsed = parsePhoneNumberFromString(normalized);

  return parsed?.country ? findCountry(parsed.country) : undefined;
}

function getFormattedPhone(value: string, country?: PhoneCountry) {
  const normalized = normalizePhoneValue(value);

  if (!normalized) return "";

  const parsed = parsePhoneNumberFromString(
    normalized,
    country?.alpha2 as CountryCode | undefined,
  );

  if (parsed?.number) return parsed.number;

  return normalized;
}

export const PhoneInput = forwardRef<HTMLInputElement, PhoneInputProps>(
  (
    {
      className,
      defaultCountry,
      disabled,
      onChange,
      onCountryChange,
      placeholder = "Enter phone number",
      value,
      ...props
    },
    ref,
  ) => {
    const [open, setOpen] = useState(false);
    const selectedCountry = useMemo(
      () => getCountryFromPhone(value) ?? findCountry(defaultCountry),
      [defaultCountry, value],
    );
    const displayValue = useMemo(
      () =>
        value
          ? new AsYouType(selectedCountry?.alpha2 as CountryCode | undefined).input(value)
          : "",
      [selectedCountry?.alpha2, value],
    );

    useEffect(() => {
      onCountryChange?.(selectedCountry);
    }, [onCountryChange, selectedCountry]);

    const emitValue = (
      nextValue: string,
      event: ChangeEvent<HTMLInputElement>,
    ) => {
      const formattedValue = getFormattedPhone(nextValue, selectedCountry);
      const syntheticEvent = {
        ...event,
        target: {
          ...event.target,
          value: formattedValue,
        },
      } as ChangeEvent<HTMLInputElement>;

      onChange?.(syntheticEvent);
    };

    const selectCountry = (country: PhoneCountry) => {
      const countryPrefix = country.countryCallingCodes[0] ?? "";
      setOpen(false);
      onCountryChange?.(country);

      if (!value && countryPrefix) {
        const syntheticEvent = {
          target: {
            value: countryPrefix,
          },
        } as ChangeEvent<HTMLInputElement>;

        onChange?.(syntheticEvent);
      }
    };

    return (
      <div className={cn("phone-input", className)} data-disabled={disabled}>
        <Popover open={open} onOpenChange={setOpen}>
          <PopoverTrigger asChild>
            <button
              type="button"
              className="phone-input__country"
              disabled={disabled}
              aria-label="Select phone country"
            >
              {selectedCountry ? (
                <CircleFlag
                  className="phone-input__flag"
                  countryCode={selectedCountry.alpha2.toLowerCase()}
                  height={20}
                />
              ) : (
                <Globe2 size={18} aria-hidden="true" />
              )}
              <span>{selectedCountry?.countryCallingCodes?.[0] ?? "+..."}</span>
              <ChevronsUpDown size={14} aria-hidden="true" />
            </button>
          </PopoverTrigger>
          <PopoverContent className="phone-input__content" align="start">
            <Command className="phone-input__command">
              <CommandInput placeholder="Search country..." />
              <CommandList className="phone-input__list">
                <CommandEmpty className="phone-input__empty">
                  No country found.
                </CommandEmpty>
                <CommandGroup>
                  {phoneCountries.map((country: PhoneCountry) => (
                    <CommandItem
                      key={country.alpha2}
                      value={`${country.name} ${country.alpha2} ${country.alpha3} ${country.countryCallingCodes.join(" ")}`}
                      className="phone-input__item"
                      onSelect={() => selectCountry(country)}
                    >
                      <span className="phone-input__item-main">
                        <CircleFlag
                          className="phone-input__flag"
                          countryCode={country.alpha2.toLowerCase()}
                          height={20}
                        />
                        <span>{country.name}</span>
                      </span>
                      <span className="phone-input__calling-code">
                        {country.countryCallingCodes[0]}
                      </span>
                      <Check
                        size={16}
                        className={
                          country.alpha2 === selectedCountry?.alpha2 ? "is-selected" : ""
                        }
                        aria-hidden="true"
                      />
                    </CommandItem>
                  ))}
                </CommandGroup>
              </CommandList>
            </Command>
          </PopoverContent>
        </Popover>
        <input
          ref={ref}
          type="tel"
          autoComplete="tel"
          inputMode="tel"
          disabled={disabled}
          value={displayValue}
          placeholder={placeholder}
          onChange={(event) => emitValue(event.target.value, event)}
          {...props}
        />
      </div>
    );
  },
);

PhoneInput.displayName = "PhoneInput";
