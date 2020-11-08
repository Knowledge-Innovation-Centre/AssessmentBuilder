
export const state = () => ({
  assessment: {},
  report: {},
  formId: {},
  formSettings: {},
  currentPage: 0,
  user: null,
  errors: [],
  exportEnabled: false,
  settings: {
    aoat_max_pages: 5,
    aoat_max_questions_per_page: 10,
    aoat_max_items_per_column: 4,
    aoat_page_for_assessments: null,
    aoat_redirect_after_completion: false,
    aoat_show_link_button: false,
  },
})
