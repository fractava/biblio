<template>
  <div class="md-layout md-gutter md-alignment-top-center">
    <div class="md-layout-item md-medium-size-50 md-small-size-100 md-xsmall-size-100">
			<md-card>
				<md-card-header>
					<div class="md-title">Zurückgeben</div>
				</md-card-header>
				<md-card-content>
				</md-card-content>
			</md-card>
		</div>
    <div class="md-layout-item md-medium-size-50 md-small-size-100 md-xsmall-size-100">
			<md-card>
				<md-card-header>
					<div class="md-title">Ausleihen</div>
				</md-card-header>
				<md-card-content>
					<md-autocomplete v-model="selectedStudent" :md-options="students">
						<label>Student</label>
						<template slot="md-autocomplete-item" slot-scope="{ item, term }">
							<md-highlight-text :md-term="term">{{ item }}</md-highlight-text>
						</template>

						<template slot="md-autocomplete-empty" slot-scope="{ term }">
							No students matching "{{ term }}" were found.
						</template>
					</md-autocomplete>
				</md-card-content>
			</md-card>
		</div>
	</div>
</template>

<script>
export default {
	data: function() {
		return {
			selectedStudent: "",
			students: [
				"baum",
				"test",
			],
		};
	}
};
</script>
