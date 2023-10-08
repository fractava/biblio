export default {
	methods: {
		ucfirst(str) {
			if (!str) {
				return "";
			}
			// converting first letter to uppercase
			return str.charAt(0).toUpperCase() + str.slice(1);
		},

		hasJsonStructure(str) {
			if (typeof str !== "string") {
				return false;
			}
			try {
				const result = JSON.parse(str);
				const type = Object.prototype.toString.call(result);
				return type === "[object Object]"
					|| type === "[object Array]";
			} catch (err) {
				return false;
			}
		},

		range(start, stop, step) {
			if (typeof stop == "undefined") {
				// one param defined
				stop = start;
				start = 0;
			}

			if (typeof step == "undefined") {
				step = 1;
			}

			if ((step > 0 && start >= stop) || (step < 0 && start <= stop)) {
				return [];
			}

			const result = [];
			for (let i = start; step > 0 ? i < stop : i > stop; i += step) {
				result.push(i);
			}

			return result;
		},
	},
};
