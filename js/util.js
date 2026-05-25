function getBaseUrl() {
  const parts = window.location.pathname.split('/').filter(Boolean);
  const phpIndex = parts.lastIndexOf('php');
  return phpIndex > 0 ? '/' + parts.slice(0, phpIndex).join('/') + '/' : '/';
}