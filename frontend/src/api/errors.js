export function getValidationErrors(error) {
  return error?.response?.data?.errors ?? {}
}

export function getErrorMessage(error, fallback = 'Wystąpił błąd') {
  const firstError = Object.values(getValidationErrors(error))[0]

  if (Array.isArray(firstError) && firstError.length > 0) {
    return firstError[0]
  }

  return error?.response?.data?.message ?? fallback
}
