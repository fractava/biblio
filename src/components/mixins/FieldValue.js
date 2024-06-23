export default {
	props: {
		allowValueEdit: {
			type: Boolean,
			deafult: false,
		},
		name: {
			type: String,
			default: "",
		},
		placeholder: {
			type: String,
			default: t("biblio", "Value"),
		},
		fieldType: {
			type: Object,
			default: {},
		},
	},
};
