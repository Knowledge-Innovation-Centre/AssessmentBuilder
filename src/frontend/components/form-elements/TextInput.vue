<template>
  <div>
    <label>
      {{ object.label }} <template v-if="object.required">*</template>
    </label>
    <input class="aoat-w-full"
           :class="hasError ? 'aoat-border-red-400' : ''"
           v-model="value" :placeholder="object.placeholder"
           type="text" >
  </div>
</template>

<script>

  export default {

    name: 'TextInput',

    components: {

    },

    props: {
      object: {
        type: Object,
        required: true,
      },
      hasError: {
        type: Boolean,
        required: false,
        default: false,
      }
    },

    computed: {
      user() {
        return this.$store.state.user
      },
      value: {
        get () {
          return this.$store.state.assessment[this.object.key]
        },
        set (newValue) {
          return this.$store.dispatch('updateValue', { key: this.object.key, value: newValue})
        }
      }
    },

    data () {
      return {
      };
    },
    watch: {
      user() {
          this.setInitialData();
      }
    },

    methods: {
      setInitialData() {
        if (this.object.type === 'first_last_name') {
          if (this.user) {
            this.$store.dispatch('updateValue', {
              key: this.object.key,
              value: this.user.first_name + ' ' + this.user.last_name
            })
          }
        }
      }
    }
  };
</script>
