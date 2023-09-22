<template>
	<div class="navigationSelectContainer">
		<div class="navigationSelectContainerIcon">
			<NcLoadingIcon v-if="loading" />
			<slot v-else name="icon" />
		</div>
		<vueSelect class="navigationSelectSelect" :options="['test', 'test 2']" placeholder="Select Library" />
		<NcActions ref="actions"
			:inline="1"
			class="app-navigation-entry__actions"
			container="#app-navigation-vue">
			<NcActionButton :aria-label="buttonAriaLabel"
				@click="handleButton">
				<template #icon>
					<slot name="button-icon" />
				</template>
				{{ editLabel }}
			</NcActionButton>
		</NcActions>
	</div>
</template>

<script>
import vueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import NcLoadingIcon from '@nextcloud/vue/dist/Components/NcLoadingIcon.js';
import NcActions from '@nextcloud/vue/dist/Components/NcActions.js';
import NcActionButton from '@nextcloud/vue/dist/Components/NcActionButton.js';

export default {
	components: {
		vueSelect,
		NcLoadingIcon,
        NcActions,
        NcActionButton,
	},
	props: {
		/**
		 * Displays a loading animated icon on the left of the element
		 * instead of the icon.
		 */
		loading: {
			type: Boolean,
			default: false,
		},

		buttonAriaLabel: {
			type: String,
			default: "",
		},
	},
	methods: {
		handleButton() {

		},
	},
};
</script>

<style scoped>
.navigationSelectContainer {
    display: flex;
    flex-direction: row;
    width: 100%;
    --vs-search-input-bg: transparent;
}

.navigationSelectSelect {
    width: 100%;
}

.navigationSelectContainerIcon {
    display: flex;
    align-items: center;
    flex: 0 0 44px;
    justify-content: center;
    width: 44px;
    height: 44px;
}
</style>

<style lang="scss">
.navigationSelectSelect {
    .vs__selected-options {
        height: 30px;
    }

    .vs__selected {
        margin-top: 2px;
        height: 100%;
    }

    .vs__dropdown-option {
        padding-top: 4px;
        padding-bottom: 4px;
    }

    .vs__search, .vs__search:focus {
        margin-top: 0px;
        height: 100%;
        border: none;
    }

    .vs__dropdown-menu {
        border-color: var(--vs-border-color) !important;
    }
}
</style>