Table departament_types {
  id integer [primary key]
  name text
  created_at datetime
  updated_at datetime
}

Table departaments {
  id integer [primary key]
  name text
  departament_type_id integer [ref: > departament_types.id]
  created_at datetime
  updated_at datetime
}

Table users {
  id integer [primary key]
  departament_id integer [ref: > departaments.id]
  created_at datetime
  updated_at datetime
}

Table roles {
  id integer [primary key]
  created_at datetime
  updated_at datetime
}

Table user_roles {
  role_id integer [ref: > roles.id]
  user_id integer [ref: > users.id]
}

Table forms {
  id integer [primary key]
  name text
  periodicity ineger
  periodicity_step integer
  deadline integer
  type integer
  is_active boolean
  is_editable boolean
  created_at datetime
  updated_at datetime
}

Table form_results {
  id integer [primary key]
  user_id integer [ref: > users.id]
  event_id integer [ref: > events.id]
  field_id integer [ref: > fields.id]
  index integer
  value text
}

Table form_departament_types {
  form_id integer [ref: > forms.id]
  departament_type_id integer [ref: > departament_types.id]
}

Table events {
  id integer [primary key]
  form_id integer [ref: > forms.id]
  departament_id integer [ref: > departaments.id]
  form_structure text
  filled_at datetime
  refilled_at datetime
  saved_structure text
  created_at datetime
  updated_at datetime
}

Table collections {
  id integer [primary key]
  name text
  created_at datetime
  updated_at datetime
}

Table collection_values {
  id integer [primary key]
  collection_id integer [ref: > collections.id]
  value text
  created_at datetime
  updated_at datetime
}

Table fields {
  id integer [primary key]
  form_id integer [ref: > forms.id]
  name text
  group text
  type text
  sort integer
  collection_id integer [ref: > collections.id]
  created_at datetime
  updated_at datetime
}

Table prepared_events {
  id integer [primary key]
  event_id integer [ref: > events.id, null, note: "nullable"]
  user_fullname text
  departament_name text
  form_name text
  event_created_at datetime
  event_filled_at datetime
  event_refilled_at datetime
}

Table prepared_form_results {
  id integer [primary key]
  prepared_event_id integer [ref: > prepared_events.id]
  field_id integer [ref: > fields.id, null, note: "nullable"]
  row_key_structure text
  row_key_first text
  group_key_structure text
  key text
  value text
  index integer
}


Ref: "form_departament_types"."form_id" < "user_roles"."role_id"

Ref: "events"."created_at" < "forms"."id"