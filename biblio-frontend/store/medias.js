export const state = () => ({
	medias: [
		{
			id: 0,
			name: "Mathe 11",
			instances: [
				{id: 0, barcode: "M11-1"},
				{id: 1, barcode: "M11-2"},
			],
		},
	],
});

export const mutations = {
	createMedia(options) {
		state.medias.push(options);
	},
};

export const actions = {
	createMedia(options) {
		// send to server
		state.medias.push(options);
	},
	createInstance(options) {
		console.log(options);
	},
	modifyMedia(options) {
		console.log(options);
	},
	modifyInstance(options) {
		console.log(options);
	},
};
