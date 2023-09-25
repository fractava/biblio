function updatePathParams($router, newParams) {
	// Retrieve current params
	const currentParams = $router.currentRoute.params;

	// Create new params object
	const mergedParams = { ...currentParams, newParams };

	// When router is not supplied path or name,
	// it simply tries to update current route with new params or query
	// Almost everything is optional.
	$router.push({ params: mergedParams });
}

export { updatePathParams };
