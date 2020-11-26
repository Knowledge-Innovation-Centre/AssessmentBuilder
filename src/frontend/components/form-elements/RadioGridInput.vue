<template>
  <div>
    {{ object.label }}
    <table
      class="aoat-w-full"
      :style="getWidthStyle"
      :class="hasError ? 'aoat-border-red-400' : ''"
    >
      <thead>
        <tr>
          <th />
          <th
            v-for="optionVertical in optionsVertical"
            :key="optionVertical.id"
          >
            <span :style="'color: ' + optionVertical.color">{{
              optionVertical.name
            }}</span>
            <span
              v-if="optionVertical.icon"
              class="dashicons"
              :class="optionVertical.icon"
            />
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="optionHorizontal in optionsHorizontal"
          :key="optionHorizontal.id"
        >
          <td>{{ optionHorizontal.name }}</td>
          <td
            v-for="optionVertical in optionsVertical"
            :key="optionVertical.id"
          >
            <input
              v-model="value[optionHorizontal.id]"
              type="radio"
              :name="optionHorizontal.id"
              :value="optionVertical.id"
            />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: "RadioGridInput",

  components: {},

  props: {
    object: {
      type: Object,
      required: true
    },
    hasError: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data() {
    return {
      value: {}
    };
  },
  computed: {
    optionsHorizontal() {
      return this.object.optionsHorizontal;
    },
    optionsVertical() {
      return this.object.optionsVertical;
    },
    getWidthStyle() {
      if (this.object.maxWidth) {
        return (
          "max-width:" + this.object.maxWidth + this.object.maxWidthUnit + ";"
        );
      }
      return "";
    }
  },

  watch: {
    value: {
      deep: true,
      handler() {
        return this.$store.dispatch("updateValue", {
          key: this.object.key,
          value: this.value,
          score: this.score
        });
      }
    }
  },
  mounted() {
    for (let optionHorizontal of this.optionsHorizontal) {
      this.$set(this.value, optionHorizontal.id, null);
    }
  },
  methods: {
    // updateVuex() {
    //   return this.$store.dispatch('updateValue', { key: this.object.key, value: this.value, score: this.score})
    // }
  }
};
</script>
