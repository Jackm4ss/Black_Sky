import { forwardRef, useMemo, useRef, useState } from "react";
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

const phoneCountriesByCallingCodeLength = [...phoneCountries].sort(
  (first: PhoneCountry, second: PhoneCountry) =>
    countryCallingDigits(second).length - countryCallingDigits(first).length,
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

function countryCallingDigits(country?: PhoneCountry) {
  return country?.countryCallingCodes[0]?.replace(/\D/g, "") ?? "";
}

function getNationalPhoneValue(
  value: string,
  country: PhoneCountry | undefined,
  isInternationalEntry: boolean,
) {
  const trimmed = value.trim();

  if (!trimmed) return "";

  const countryPrefix = countryCallingDigits(country);

  if (trimmed.startsWith("+") || trimmed.startsWith("00")) {
    const internationalDigits = trimmed
      .replace(/^00/, "")
      .replace(/\D/g, "");

    if (countryPrefix && internationalDigits.startsWith(countryPrefix)) {
      return internationalDigits.slice(countryPrefix.length);
    }

    return internationalDigits;
  }

  const localDigits = trimmed.replace(/\D/g, "");

  if (isInternationalEntry && countryPrefix) {
    if (localDigits.startsWith(countryPrefix)) {
      return localDigits.slice(countryPrefix.length);
    }

    if (countryPrefix.startsWith(localDigits)) {
      return localDigits;
    }
  }

  return localDigits;
}

export function getNationalPhoneNumber(value?: string | null, countryCode?: string) {
  const trimmed = value?.trim() ?? "";

  if (!trimmed) return "";

  return getNationalPhoneValue(
    trimmed,
    findCountry(countryCode),
    trimmed.startsWith("+") || trimmed.startsWith("00"),
  );
}

export function getPhoneCountryCode(value?: string | null) {
  const trimmed = value?.trim() ?? "";

  if (!trimmed.startsWith("+") && !trimmed.startsWith("00")) return undefined;

  const internationalDigits = trimmed
    .replace(/^00/, "")
    .replace(/\D/g, "");

  const country = phoneCountriesByCallingCodeLength.find((item) => {
    const prefix = countryCallingDigits(item);

    return prefix && internationalDigits.startsWith(prefix);
  });

  return country?.alpha2.toUpperCase();
}

export function getInternationalPhoneNumber(value?: string | null, countryCode?: string) {
  const country = findCountry(countryCode);
  const nationalValue = getNationalPhoneValue(value ?? "", country, false);

  if (!nationalValue) return "";

  const parsed = parsePhoneNumberFromString(
    nationalValue,
    country?.alpha2 as CountryCode | undefined,
  );

  if (parsed?.number) return parsed.number;

  const countryPrefix = country?.countryCallingCodes[0];

  return countryPrefix
    ? `${countryPrefix}${nationalValue.replace(/^0+/, "")}`
    : nationalValue;
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
    const isInternationalEntryRef = useRef(false);
    const selectedCountry = useMemo(
      () => findCountry(defaultCountry),
      [defaultCountry],
    );
    const displayValue = useMemo(
      () => {
        const nationalValue = getNationalPhoneNumber(value, selectedCountry?.alpha2);

        return nationalValue
          ? new AsYouType(selectedCountry?.alpha2 as CountryCode | undefined).input(nationalValue)
          : "";
      },
      [selectedCountry?.alpha2, value],
    );

    const emitValue = (
      nextValue: string,
      event: ChangeEvent<HTMLInputElement>,
    ) => {
      const trimmedValue = nextValue.trim();

      if (!trimmedValue) {
        isInternationalEntryRef.current = false;
      } else if (trimmedValue.startsWith("+") || trimmedValue.startsWith("00")) {
        isInternationalEntryRef.current = true;
      }

      const formattedValue = getNationalPhoneValue(
        nextValue,
        selectedCountry,
        isInternationalEntryRef.current,
      );

      const countryPrefix = countryCallingDigits(selectedCountry);
      const formattedDigits = formattedValue.replace(/\D/g, "");

      if (
        isInternationalEntryRef.current &&
        formattedDigits &&
        (!countryPrefix || !countryPrefix.startsWith(formattedDigits))
      ) {
        isInternationalEntryRef.current = false;
      }

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
      setOpen(false);
      onCountryChange?.(country);
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
