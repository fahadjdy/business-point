/**
 * Common formatting utilities
 */

/**
 * Format date to consistent format: 30-jan-2025 12:26PM
 */
export const formatDate = (dateString) => {
  if (!dateString) return '';

  const date = new Date(dateString);

  // Format date part: 30-jan-2025
  const datePart = date.toLocaleDateString('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  }).toLowerCase();

  // Format time part: 12:26PM
  const timePart = date.toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  });

  return `${datePart} ${timePart}`;
};

/**
 * Format date only: 30-jan-2025
 */
export const formatDateOnly = (dateString) => {
  if (!dateString) return '';

  const date = new Date(dateString);
  return date.toLocaleDateString('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  }).toLowerCase();
};

/**
 * Format time only: 12:26PM
 */
export const formatTimeOnly = (dateString) => {
  if (!dateString) return '';

  const date = new Date(dateString);
  return date.toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  });
};