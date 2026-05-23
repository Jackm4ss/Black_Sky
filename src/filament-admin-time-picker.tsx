import * as React from "react";
import * as DialogPrimitive from "@radix-ui/react-dialog";
import { CheckIcon, ChevronDownIcon, ClockIcon, KeyboardIcon, XIcon } from "lucide-react";
import { createRoot, type Root } from "react-dom/client";

import "./styles/filament-admin-time-picker.css";

type TimeValue = {
  hour: number;
  minute: number;
};

type TimePickerProps = {
  inputId: string;
  value: string;
  emptyLabel?: string;
  ariaLabel?: string;
  dialogTitle?: string;
  dialogDescription?: string;
};

type DialStyle = React.CSSProperties & {
  "--bsa-time-angle"?: string;
  "--bsa-time-hand-length"?: string;
  "--bsa-time-x"?: string;
  "--bsa-time-y"?: string;
};

const roots = new WeakMap<Element, Root>();
const hourOptions = Array.from({ length: 24 }, (_, index) => index);
const minuteOptions = Array.from({ length: 12 }, (_, index) => index * 5);
const defaultTime: TimeValue = { hour: 20, minute: 0 };

function clamp(value: number, min: number, max: number): number {
  return Math.min(Math.max(value, min), max);
}

function pad(value: number): string {
  return value.toString().padStart(2, "0");
}

function parseTimeValue(value: string): TimeValue | undefined {
  const [hourValue, minuteValue] = value.split(":");
  const hour = Number(hourValue);
  const minute = Number(minuteValue);

  if (!Number.isInteger(hour) || !Number.isInteger(minute)) {
    return undefined;
  }

  if (hour < 0 || hour > 23 || minute < 0 || minute > 59) {
    return undefined;
  }

  return { hour, minute };
}

function toInputValue(time: TimeValue | undefined): string {
  return time ? `${pad(time.hour)}:${pad(time.minute)}` : "";
}

function formatTimeLabel(time: TimeValue | undefined, emptyLabel: string): string {
  return time ? toInputValue(time) : emptyLabel;
}

function syncInput(inputId: string, value: string): void {
  const input = document.getElementById(inputId) as HTMLInputElement | null;

  if (!input || input.value === value) {
    return;
  }

  input.value = value;
  input.setAttribute("value", value);
  input.dispatchEvent(new Event("input", { bubbles: true }));
  input.dispatchEvent(new Event("change", { bubbles: true }));
}

function getClockPosition(value: number, radius: number): DialStyle {
  const angle = (value / 12) * Math.PI * 2 - Math.PI / 2;

  return {
    "--bsa-time-x": `${Math.cos(angle) * radius}px`,
    "--bsa-time-y": `${Math.sin(angle) * radius}px`,
  };
}

function getHandStyle(time: TimeValue, activePart: "hour" | "minute"): DialStyle {
  const dialValue = activePart === "hour" ? time.hour % 12 : time.minute / 5;
  const hourRadius = time.hour > 0 && time.hour <= 12 ? 64 : 98;
  const angle = dialValue * 30 - 90;

  return {
    "--bsa-time-angle": `${angle}deg`,
    "--bsa-time-hand-length": `${activePart === "hour" ? hourRadius : 98}px`,
  };
}

function TimePicker({
  inputId,
  value,
  emptyLabel = "Pick start time",
  ariaLabel = "Select start time",
  dialogTitle = "Select start time",
  dialogDescription = "Choose the time shown on the public event schedule.",
}: TimePickerProps) {
  const [open, setOpen] = React.useState(false);
  const [selectedTime, setSelectedTime] = React.useState<TimeValue | undefined>(() => parseTimeValue(value));
  const [draftTime, setDraftTime] = React.useState<TimeValue>(() => parseTimeValue(value) ?? defaultTime);
  const [activePart, setActivePart] = React.useState<"hour" | "minute">("hour");
  const [displayMode, setDisplayMode] = React.useState<"dial" | "input">("dial");

  React.useEffect(() => {
    const nextTime = parseTimeValue(value);

    setSelectedTime(nextTime);
    setDraftTime(nextTime ?? defaultTime);
  }, [value]);

  React.useEffect(() => {
    const input = document.getElementById(inputId) as HTMLInputElement | null;

    if (!input) {
      return;
    }

    const handleInputChange = () => {
      const nextTime = parseTimeValue(input.value);

      setSelectedTime(nextTime);
      setDraftTime(nextTime ?? defaultTime);
    };

    input.addEventListener("input", handleInputChange);
    input.addEventListener("change", handleInputChange);

    return () => {
      input.removeEventListener("input", handleInputChange);
      input.removeEventListener("change", handleInputChange);
    };
  }, [inputId]);

  const openPicker = React.useCallback((nextOpen: boolean) => {
    setOpen(nextOpen);

    if (nextOpen) {
      setDraftTime(selectedTime ?? defaultTime);
      setActivePart("hour");
      setDisplayMode("dial");
    }
  }, [selectedTime]);

  const selectHour = React.useCallback((hour: number) => {
    setDraftTime((time) => ({ ...time, hour }));
    setActivePart("minute");
  }, []);

  const selectMinute = React.useCallback((minute: number) => {
    setDraftTime((time) => ({ ...time, minute }));
  }, []);

  const updateInputPart = React.useCallback((part: "hour" | "minute", rawValue: string) => {
    const numericValue = Number(rawValue.replace(/\D/g, ""));

    if (!Number.isFinite(numericValue)) {
      return;
    }

    setDraftTime((time) => ({
      ...time,
      [part]: part === "hour" ? clamp(numericValue, 0, 23) : clamp(numericValue, 0, 59),
    }));
  }, []);

  const applyTime = React.useCallback(() => {
    setSelectedTime(draftTime);
    syncInput(inputId, toInputValue(draftTime));
    setOpen(false);
  }, [draftTime, inputId]);

  const clearTime = React.useCallback(() => {
    setSelectedTime(undefined);
    syncInput(inputId, "");
    setOpen(false);
  }, [inputId]);

  const activeLabel = activePart === "hour" ? "Select hour" : "Select minute";

  return (
    <DialogPrimitive.Root open={open} onOpenChange={openPicker}>
      <DialogPrimitive.Trigger asChild>
        <button
          type="button"
          className="bsa-time-picker-trigger"
          data-empty={!selectedTime}
          aria-label={ariaLabel}
        >
          <ClockIcon aria-hidden="true" />
          <span>{formatTimeLabel(selectedTime, emptyLabel)}</span>
          <ChevronDownIcon aria-hidden="true" />
        </button>
      </DialogPrimitive.Trigger>
      <DialogPrimitive.Portal>
        <DialogPrimitive.Overlay className="bsa-time-picker-overlay" />
        <DialogPrimitive.Content className="bsa-time-picker-dialog" aria-describedby={undefined}>
          <header className="bsa-time-picker-header">
            <div>
              <DialogPrimitive.Title className="bsa-time-picker-title">
                {dialogTitle}
              </DialogPrimitive.Title>
              <p className="bsa-time-picker-description">
                {dialogDescription}
              </p>
            </div>
            <DialogPrimitive.Close className="bsa-time-picker-close" aria-label="Close time picker">
              <XIcon aria-hidden="true" />
            </DialogPrimitive.Close>
          </header>

          <section className="bsa-time-picker-display" aria-label={activeLabel}>
            <button
              type="button"
              className="bsa-time-picker-display-value"
              data-active={activePart === "hour"}
              onClick={() => setActivePart("hour")}
            >
              {pad(draftTime.hour)}
            </button>
            <span>:</span>
            <button
              type="button"
              className="bsa-time-picker-display-value"
              data-active={activePart === "minute"}
              onClick={() => setActivePart("minute")}
            >
              {pad(draftTime.minute)}
            </button>
            <button
              type="button"
              className="bsa-time-picker-mode"
              onClick={() => setDisplayMode((mode) => (mode === "dial" ? "input" : "dial"))}
              aria-label={displayMode === "dial" ? "Switch to keyboard time input" : "Switch to clock dial"}
            >
              {displayMode === "dial" ? <KeyboardIcon aria-hidden="true" /> : <ClockIcon aria-hidden="true" />}
            </button>
          </section>

          {displayMode === "dial" ? (
            <section className="bsa-time-picker-clock" aria-label={activeLabel}>
              <span className="bsa-time-picker-hand" style={getHandStyle(draftTime, activePart)} />
              <span className="bsa-time-picker-center" />

              {activePart === "hour"
                ? hourOptions.map((hour) => {
                    const isSelected = draftTime.hour === hour;
                    const radius = hour > 0 && hour <= 12 ? 64 : 98;

                    return (
                      <button
                        key={hour}
                        type="button"
                        className="bsa-time-picker-clock-option"
                        data-selected={isSelected}
                        style={getClockPosition(hour % 12, radius)}
                        onClick={() => selectHour(hour)}
                        aria-label={`Select ${pad(hour)} hour`}
                      >
                        {pad(hour)}
                      </button>
                    );
                  })
                : minuteOptions.map((minute) => (
                    <button
                      key={minute}
                      type="button"
                      className="bsa-time-picker-clock-option"
                      data-selected={draftTime.minute === minute}
                      style={getClockPosition(minute / 5, 98)}
                      onClick={() => selectMinute(minute)}
                      aria-label={`Select ${pad(minute)} minutes`}
                    >
                      {pad(minute)}
                    </button>
                  ))}
            </section>
          ) : (
            <section className="bsa-time-picker-input-panel" aria-label="Keyboard time input">
              <label>
                <span>Hour</span>
                <input
                  inputMode="numeric"
                  value={pad(draftTime.hour)}
                  onFocus={() => setActivePart("hour")}
                  onChange={(event) => updateInputPart("hour", event.target.value)}
                  aria-label="Start hour"
                />
              </label>
              <span>:</span>
              <label>
                <span>Minute</span>
                <input
                  inputMode="numeric"
                  value={pad(draftTime.minute)}
                  onFocus={() => setActivePart("minute")}
                  onChange={(event) => updateInputPart("minute", event.target.value)}
                  aria-label="Start minute"
                />
              </label>
            </section>
          )}

          <footer className="bsa-time-picker-footer">
            <button type="button" onClick={clearTime}>
              Clear
            </button>
            <div>
              <DialogPrimitive.Close type="button">Cancel</DialogPrimitive.Close>
              <button type="button" onClick={applyTime}>
                <CheckIcon aria-hidden="true" />
                Apply
              </button>
            </div>
          </footer>
        </DialogPrimitive.Content>
      </DialogPrimitive.Portal>
    </DialogPrimitive.Root>
  );
}

export function mountAdminTimePickers(): void {
  document.querySelectorAll<HTMLElement>("[data-bsa-time-picker]").forEach((element) => {
    const inputId = element.dataset.timeInput ?? "";
    const input = document.getElementById(inputId) as HTMLInputElement | null;

    if (!inputId || !input) {
      return;
    }

    let root = roots.get(element);

    if (!root) {
      root = createRoot(element);
      roots.set(element, root);
    }

    root.render(
      <TimePicker
        inputId={inputId}
        value={input.value}
        emptyLabel={element.dataset.timeEmptyLabel}
        ariaLabel={element.dataset.timeAriaLabel}
        dialogTitle={element.dataset.timeTitle}
        dialogDescription={element.dataset.timeDescription}
      />,
    );
  });
}
