#!/bin/bash
find resources/views -type f -name "*.blade.php" -exec sed -i \
  -e 's/rounded-lg border border-slate-200 bg-white/nb-card/g' \
  -e 's/rounded-lg border border-slate-200 bg-slate-50/nb-card bg-brand-lime/g' \
  -e 's/rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-50/nb-btn nb-btn-white/g' \
  -e 's/w-full rounded-md bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700/nb-btn nb-btn-primary w-full/g' \
  -e 's/rounded-md bg-teal-600 px-4 py-3 text-sm font-semibold text-white hover:bg-teal-700/nb-btn nb-btn-primary/g' \
  -e 's/rounded-md border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-800 hover:bg-slate-50/nb-btn nb-btn-white/g' \
  -e 's/w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-teal-500 focus:ring-teal-500/nb-input w-full/g' \
  -e 's/border-slate-[1-9]00/border-ink/g' \
  -e 's/text-slate-900/text-ink font-black/g' \
  -e 's/text-slate-950/text-ink font-black/g' \
  -e 's/text-slate-600/text-ink\/70 font-semibold/g' \
  -e 's/text-slate-500/text-ink\/60 font-semibold/g' \
  -e 's/bg-slate-50/bg-paper/g' \
  -e 's/bg-slate-100/bg-brand-sky\/10/g' \
  -e 's/bg-white/bg-white/g' \
  {} +
echo "Style conversion completed."
