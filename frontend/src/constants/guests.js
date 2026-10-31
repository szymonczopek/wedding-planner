export const acquaintanceTypes = [
  {label: 'Świadek', value: 'witness'},
  {label: 'Rodzina bliska', value: 'close_family'},
  {label: 'Rodzina daleka', value: 'distant_family'},
  {label: 'Znajomy bliski', value: 'close_friend'},
  {label: 'Znajomy daleki', value: 'distant_friend'},
  {label: 'Sąsiad', value: 'neighbor'},
  {label: 'Osoba towarzysząca', value: 'plus_one'},
]

export const diets = [
  {label: 'Standardowa', value: 'standard'},
  {label: 'Wegetariańska', value: 'vegetarian'},
  {label: 'Wegańska', value: 'vegan'},
  {label: 'Keto', value: 'keto'},
  {label: 'Lekkostrawna', value: 'light'},
  {label: 'Bezglutenowa', value: 'gluten_free'},
  {label: 'Bez laktozy', value: 'lactose_free'},
  {label: 'Bez orzechów', value: 'nut_free'},
  {label: 'Bez ryb i owoców morza', value: 'fish_and_seafood_free'},
  {label: 'Halal', value: 'halal'},
  {label: 'Koszerna', value: 'kosher'},
  {label: 'Menu dziecięce', value: 'kids_menu'},
]

export const acquaintanceTypeSeverities = {
  witness: 'warn',
  close_family: 'success',
  distant_family: 'info',
  close_friend: 'success',
  distant_friend: 'info',
  neighbor: 'secondary',
  plus_one: 'contrast',
}

export const dietSeverities = {
  standard: 'secondary',
  vegetarian: 'success',
  vegan: 'success',
  keto: 'info',
  light: 'info',
  gluten_free: 'warn',
  lactose_free: 'warn',
  nut_free: 'warn',
  fish_and_seafood_free: 'warn',
  halal: 'info',
  kosher: 'info',
  kids_menu: 'contrast',
}

export function optionLabel(options, value) {
  return options.find((option) => option.value === value)?.label ?? value ?? '—'
}
