import * as React from "react";
import { createRoot, type Root } from "react-dom/client";

import {
  getInternationalPhoneNumber,
  getNationalPhoneNumber,
  getPhoneCountryCode,
  PhoneInput,
  type PhoneCountry,
} from "./app/components/ui/phone-input";

import "./styles/filament-admin-phone-input.css";

type AdminPhoneInputProps = {
  countryInputId: string;
  phoneInputId: string;
};

const roots = new WeakMap<Element, Root>();

function syncInput(inputId: string, value: string): void {
  const input = document.getElementById(inputId) as HTMLInputElement | null;

  if (!input || input.value === value) {
    return;
  }

  input.value = value;
  input.dispatchEvent(new Event("input", { bubbles: true }));
  input.dispatchEvent(new Event("change", { bubbles: true }));
}

function currentInputValue(inputId: string): string {
  return (document.getElementById(inputId) as HTMLInputElement | null)?.value ?? "";
}

function AdminPhoneInput({ countryInputId, phoneInputId }: AdminPhoneInputProps) {
  const [phoneValue, setPhoneValue] = React.useState(() => currentInputValue(phoneInputId));
  const [phoneCountryCode, setPhoneCountryCode] = React.useState(() => {
    const rawPhone = currentInputValue(phoneInputId);

    return getPhoneCountryCode(rawPhone) ?? currentInputValue(countryInputId) ?? "MY";
  });

  React.useEffect(() => {
    const phoneInput = document.getElementById(phoneInputId) as HTMLInputElement | null;

    if (!phoneInput) {
      return;
    }

    const handlePhoneInputChange = () => {
      const rawPhone = phoneInput.value;
      const nextCountryCode = getPhoneCountryCode(rawPhone);

      setPhoneValue(rawPhone);

      if (nextCountryCode) {
        setPhoneCountryCode(nextCountryCode);
      }
    };

    handlePhoneInputChange();
    phoneInput.addEventListener("input", handlePhoneInputChange);
    phoneInput.addEventListener("change", handlePhoneInputChange);

    return () => {
      phoneInput.removeEventListener("input", handlePhoneInputChange);
      phoneInput.removeEventListener("change", handlePhoneInputChange);
    };
  }, [phoneInputId]);

  const nationalPhoneValue = React.useMemo(
    () => getNationalPhoneNumber(phoneValue, phoneCountryCode),
    [phoneCountryCode, phoneValue],
  );

  const updatePhone = React.useCallback(
    (nationalValue: string, countryCode = phoneCountryCode) => {
      setPhoneValue(nationalValue);
      syncInput(phoneInputId, getInternationalPhoneNumber(nationalValue, countryCode));
    },
    [phoneCountryCode, phoneInputId],
  );

  const updatePhoneCountry = React.useCallback(
    (country: PhoneCountry | undefined) => {
      if (!country) {
        return;
      }

      const nextCountryCode = country.alpha2.toUpperCase();

      setPhoneCountryCode(nextCountryCode);
      syncInput(phoneInputId, getInternationalPhoneNumber(nationalPhoneValue, nextCountryCode));
    },
    [nationalPhoneValue, phoneInputId],
  );

  return (
    <PhoneInput
      defaultCountry={phoneCountryCode}
      value={nationalPhoneValue}
      onChange={(event) => updatePhone(event.target.value)}
      onCountryChange={updatePhoneCountry}
      placeholder="Enter phone number"
    />
  );
}

export function mountAdminPhoneInputs(): void {
  document.querySelectorAll<HTMLElement>("[data-bsa-phone-input]").forEach((element) => {
    const countryInputId = element.dataset.countryInput ?? "";
    const phoneInputId = element.dataset.phoneInput ?? "";

    if (!countryInputId || !phoneInputId) {
      return;
    }

    let root = roots.get(element);

    if (!root) {
      root = createRoot(element);
      roots.set(element, root);
    }

    root.render(<AdminPhoneInput countryInputId={countryInputId} phoneInputId={phoneInputId} />);
  });
}
