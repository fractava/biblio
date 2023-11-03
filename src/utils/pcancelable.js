export function stripCanceledError(promise) {
	return new Promise((resolve, reject) => {
		promise.then((result) => {
			resolve(result);
		})
			.catch((error) => {
				if (!promise.isCanceled) {
					reject(error);
				}
			});
	});
}
