<template>
  <div>
    <label>
      {{ object.label }} <template v-if="object.required">*</template>
    </label>
    <input
      v-model="value"
      class="aoat-w-full"
      :style="getWidthStyle"
      :class="hasError ? 'aoat-border-red-400' : ''"
      :placeholder="object.placeholder"
      type="text"
    />
  </div>
</template>

<script>
export default {
  name: "TextInput",

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
    return {};
  },

  computed: {
    user() {
      return this.$store.state.user;
    },
    multiple() {
      return this.object.multiple ?? false;
    },
    value: {
      get() {
        return this.$store.state.assessment[this.object.key];
      },
      set(newValue) {
        return this.$store.dispatch("updateValue", {
          key: this.object.key,
          value: newValue
        });
      }
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
    user: {
      deep: true,
      handler() {
        this.setInitialData();
      }
    }
  },
  mounted() {
    if (this.user) {
      this.setInitialData();
    }
  },

  methods: {
    setInitialData() {
      if (this.object.type === "first_last_name") {
        if (this.user) {
          this.$store.dispatch("updateValue", {
            key: this.object.key,
            value: this.user.first_name + " " + this.user.last_name
          });
        }
      }
    }
  }
};
</script>
