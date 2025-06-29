--
-- PostgreSQL database dump
--

-- Dumped from database version 13.17
-- Dumped by pg_dump version 13.17

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: attachments; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: attachmentable; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: bot_users; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: bot_user_notifications; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: bot_user_questions; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: username
--

INSERT INTO public.cache (key, value, expiration) VALUES ('User.getDepartament.v0.1', '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IjtpOjI7czoxMToiZGlzdHJpY3RfaWQiO2k6MztzOjQ6InNvcnQiO2k6NDtzOjY6InJhdGluZyI7aTo1O3M6MzoiaW5uIjtpOjY7czo2OiJkYWRhdGEiO2k6NztzOjc6ImFkZHJlc3MiO2k6ODtzOjM6ImxhdCI7aTo5O3M6MzoibG9uIjtpOjEwO3M6NDoib2twbyI7aToxMTtzOjE3OiJzaG93X2luX2Rhc2hib2FyZCI7aToxMjtzOjEwOiJmZWRlcmF0aW9uIjtpOjEzO3M6OToicGFyZW50X2lkIjtpOjE0O3M6NToicGhvbmUiO2k6MTU7czoxNjoiY29udGFjdF9mdWxsbmFtZSI7aToxNjtzOjU6ImVtYWlsIjtpOjE3O3M6MTQ6ImVtYWlsX2Z1bGxuYW1lIjt9czoxMDoiACoAZ3VhcmRlZCI7YToxOntpOjA7czoxOiIqIjt9czoxNzoiACoAYWxsb3dlZEZpbHRlcnMiO2E6MTk6e3M6MjoiaWQiO3M6MjY6Ik9yY2hpZFxGaWx0ZXJzXFR5cGVzXFdoZXJlIjtzOjQ6Im5hbWUiO3M6MjY6Ik9yY2hpZFxGaWx0ZXJzXFR5cGVzXElsaWtlIjtzOjE5OiJkZXBhcnRhbWVudF90eXBlX2lkIjtzOjI2OiJPcmNoaWRcRmlsdGVyc1xUeXBlc1xXaGVyZSI7czo0OiJzb3J0IjtzOjI2OiJPcmNoaWRcRmlsdGVyc1xUeXBlc1xXaGVyZSI7czo2OiJyYXRpbmciO3M6MjY6Ik9yY2hpZFxGaWx0ZXJzXFR5cGVzXFdoZXJlIjtzOjM6ImlubiI7czoyNjoiT3JjaGlkXEZpbHRlcnNcVHlwZXNcV2hlcmUiO3M6NjoiZGFkYXRhIjtzOjI2OiJPcmNoaWRcRmlsdGVyc1xUeXBlc1xJbGlrZSI7czo3OiJhZGRyZXNzIjtzOjI2OiJPcmNoaWRcRmlsdGVyc1xUeXBlc1xJbGlrZSI7czozOiJsYXQiO3M6MjY6Ik9yY2hpZFxGaWx0ZXJzXFR5cGVzXFdoZXJlIjtzOjM6ImxvbiI7czoyNjoiT3JjaGlkXEZpbHRlcnNcVHlwZXNcV2hlcmUiO3M6NDoib2twbyI7czoyNjoiT3JjaGlkXEZpbHRlcnNcVHlwZXNcV2hlcmUiO3M6MTc6InNob3dfaW5fZGFzaGJvYXJkIjtzOjI2OiJPcmNoaWRcRmlsdGVyc1xUeXBlc1xXaGVyZSI7czoxMDoidXBkYXRlZF9hdCI7czozODoiT3JjaGlkXEZpbHRlcnNcVHlwZXNcV2hlcmVEYXRlU3RhcnRFbmQiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6Mzg6Ik9yY2hpZFxGaWx0ZXJzXFR5cGVzXFdoZXJlRGF0ZVN0YXJ0RW5kIjtzOjk6InBhcmVudF9pZCI7czoyNjoiT3JjaGlkXEZpbHRlcnNcVHlwZXNcV2hlcmUiO3M6NToicGhvbmUiO3M6MjY6Ik9yY2hpZFxGaWx0ZXJzXFR5cGVzXElsaWtlIjtzOjE2OiJjb250YWN0X2Z1bGxuYW1lIjtzOjI2OiJPcmNoaWRcRmlsdGVyc1xUeXBlc1xJbGlrZSI7czo1OiJlbWFpbCI7czoyNjoiT3JjaGlkXEZpbHRlcnNcVHlwZXNcSWxpa2UiO3M6MTQ6ImVtYWlsX2Z1bGxuYW1lIjtzOjI2OiJPcmNoaWRcRmlsdGVyc1xUeXBlc1xJbGlrZSI7fXM6MTU6IgAqAGFsbG93ZWRTb3J0cyI7YToxOTp7aTowO3M6MjoiaWQiO2k6MTtzOjQ6Im5hbWUiO2k6MjtzOjE5OiJkZXBhcnRhbWVudF90eXBlX2lkIjtpOjM7czo0OiJzb3J0IjtpOjQ7czo2OiJyYXRpbmciO2k6NTtzOjM6ImlubiI7aTo2O3M6NjoiZGFkYXRhIjtpOjc7czo3OiJhZGRyZXNzIjtpOjg7czozOiJsYXQiO2k6OTtzOjM6ImxvbiI7aToxMDtzOjQ6Im9rcG8iO2k6MTE7czoxNzoic2hvd19pbl9kYXNoYm9hcmQiO2k6MTI7czoxMDoidXBkYXRlZF9hdCI7aToxMztzOjEwOiJjcmVhdGVkX2F0IjtpOjE0O3M6OToicGFyZW50X2lkIjtpOjE1O3M6NToicGhvbmUiO2k6MTY7czoxNjoiY29udGFjdF9mdWxsbmFtZSI7aToxNztzOjU6ImVtYWlsIjtpOjE4O3M6MTQ6ImVtYWlsX2Z1bGxuYW1lIjt9fQ==', 1751193182);
INSERT INTO public.cache (key, value, expiration) VALUES ('Form.canUserEdit.departament.v0.[1]', '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IjtpOjI7czoxMToiZGlzdHJpY3RfaWQiO2k6MztzOjQ6InNvcnQiO2k6NDtzOjY6InJhdGluZyI7aTo1O3M6MzoiaW5uIjtpOjY7czo2OiJkYWRhdGEiO2k6NztzOjc6ImFkZHJlc3MiO2k6ODtzOjM6ImxhdCI7aTo5O3M6MzoibG9uIjtpOjEwO3M6NDoib2twbyI7aToxMTtzOjE3OiJzaG93X2luX2Rhc2hib2FyZCI7aToxMjtzOjEwOiJmZWRlcmF0aW9uIjtpOjEzO3M6OToicGFyZW50X2lkIjtpOjE0O3M6NToicGhvbmUiO2k6MTU7czoxNjoiY29udGFjdF9mdWxsbmFtZSI7aToxNjtzOjU6ImVtYWlsIjtpOjE3O3M6MTQ6ImVtYWlsX2Z1bGxuYW1lIjt9czoxMDoiACoAZ3VhcmRlZCI7YToxOntpOjA7czoxOiIqIjt9czoxNzoiACoAYWxsb3dlZEZpbHRlcnMiO2E6MTk6e3M6MjoiaWQiO3M6MjY6Ik9yY2hpZFxGaWx0ZXJzXFR5cGVzXFdoZXJlIjtzOjQ6Im5hbWUiO3M6MjY6Ik9yY2hpZFxGaWx0ZXJzXFR5cGVzXElsaWtlIjtzOjE5OiJkZXBhcnRhbWVudF90eXBlX2lkIjtzOjI2OiJPcmNoaWRcRmlsdGVyc1xUeXBlc1xXaGVyZSI7czo0OiJzb3J0IjtzOjI2OiJPcmNoaWRcRmlsdGVyc1xUeXBlc1xXaGVyZSI7czo2OiJyYXRpbmciO3M6MjY6Ik9yY2hpZFxGaWx0ZXJzXFR5cGVzXFdoZXJlIjtzOjM6ImlubiI7czoyNjoiT3JjaGlkXEZpbHRlcnNcVHlwZXNcV2hlcmUiO3M6NjoiZGFkYXRhIjtzOjI2OiJPcmNoaWRcRmlsdGVyc1xUeXBlc1xJbGlrZSI7czo3OiJhZGRyZXNzIjtzOjI2OiJPcmNoaWRcRmlsdGVyc1xUeXBlc1xJbGlrZSI7czozOiJsYXQiO3M6MjY6Ik9yY2hpZFxGaWx0ZXJzXFR5cGVzXFdoZXJlIjtzOjM6ImxvbiI7czoyNjoiT3JjaGlkXEZpbHRlcnNcVHlwZXNcV2hlcmUiO3M6NDoib2twbyI7czoyNjoiT3JjaGlkXEZpbHRlcnNcVHlwZXNcV2hlcmUiO3M6MTc6InNob3dfaW5fZGFzaGJvYXJkIjtzOjI2OiJPcmNoaWRcRmlsdGVyc1xUeXBlc1xXaGVyZSI7czoxMDoidXBkYXRlZF9hdCI7czozODoiT3JjaGlkXEZpbHRlcnNcVHlwZXNcV2hlcmVEYXRlU3RhcnRFbmQiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6Mzg6Ik9yY2hpZFxGaWx0ZXJzXFR5cGVzXFdoZXJlRGF0ZVN0YXJ0RW5kIjtzOjk6InBhcmVudF9pZCI7czoyNjoiT3JjaGlkXEZpbHRlcnNcVHlwZXNcV2hlcmUiO3M6NToicGhvbmUiO3M6MjY6Ik9yY2hpZFxGaWx0ZXJzXFR5cGVzXElsaWtlIjtzOjE2OiJjb250YWN0X2Z1bGxuYW1lIjtzOjI2OiJPcmNoaWRcRmlsdGVyc1xUeXBlc1xJbGlrZSI7czo1OiJlbWFpbCI7czoyNjoiT3JjaGlkXEZpbHRlcnNcVHlwZXNcSWxpa2UiO3M6MTQ6ImVtYWlsX2Z1bGxuYW1lIjtzOjI2OiJPcmNoaWRcRmlsdGVyc1xUeXBlc1xJbGlrZSI7fXM6MTU6IgAqAGFsbG93ZWRTb3J0cyI7YToxOTp7aTowO3M6MjoiaWQiO2k6MTtzOjQ6Im5hbWUiO2k6MjtzOjE5OiJkZXBhcnRhbWVudF90eXBlX2lkIjtpOjM7czo0OiJzb3J0IjtpOjQ7czo2OiJyYXRpbmciO2k6NTtzOjM6ImlubiI7aTo2O3M6NjoiZGFkYXRhIjtpOjc7czo3OiJhZGRyZXNzIjtpOjg7czozOiJsYXQiO2k6OTtzOjM6ImxvbiI7aToxMDtzOjQ6Im9rcG8iO2k6MTE7czoxNzoic2hvd19pbl9kYXNoYm9hcmQiO2k6MTI7czoxMDoidXBkYXRlZF9hdCI7aToxMztzOjEwOiJjcmVhdGVkX2F0IjtpOjE0O3M6OToicGFyZW50X2lkIjtpOjE1O3M6NToicGhvbmUiO2k6MTY7czoxNjoiY29udGFjdF9mdWxsbmFtZSI7aToxNztzOjU6ImVtYWlsIjtpOjE4O3M6MTQ6ImVtYWlsX2Z1bGxuYW1lIjt9fQ==', 1751795136);
INSERT INTO public.cache (key, value, expiration) VALUES ('App\Plugins\IBKnowledgeBase\Orchid\Screens\ArticleListScreen::layout[authors]', 'TzoyOToiSWxsdW1pbmF0ZVxTdXBwb3J0XENvbGxlY3Rpb24iOjI6e3M6ODoiACoAaXRlbXMiO2E6MTp7aToxO3M6Mzg6ItCY0LLQsNC90L7QsiDQmNCy0LDQvSDQmNCy0LDQvdC+0LLQuNGHIjt9czoyODoiACoAZXNjYXBlV2hlbkNhc3RpbmdUb1N0cmluZyI7YjowO30=', 1751276902);
INSERT INTO public.cache (key, value, expiration) VALUES ('App\Plugins\IBKnowledgeBase\Orchid\Screens\ArticleListScreen::layout[parents]', 'TzozOToiSWxsdW1pbmF0ZVxEYXRhYmFzZVxFbG9xdWVudFxDb2xsZWN0aW9uIjoyOntzOjg6IgAqAGl0ZW1zIjthOjA6e31zOjI4OiIAKgBlc2NhcGVXaGVuQ2FzdGluZ1RvU3RyaW5nIjtiOjA7fQ==', 1751276902);


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: collections; Type: TABLE DATA; Schema: public; Owner: username
--

INSERT INTO public.collections (id, name, created_at, updated_at) VALUES (1, 'Ответственные лица Учреждений', '2025-06-29 12:37:28', '2025-06-29 12:37:28');
INSERT INTO public.collections (id, name, created_at, updated_at) VALUES (2, 'Цель посещения', '2025-06-29 12:43:23', '2025-06-29 12:43:23');
INSERT INTO public.collections (id, name, created_at, updated_at) VALUES (3, 'Статус посещения', '2025-06-29 12:43:42', '2025-06-29 12:43:42');


--
-- Data for Name: collection_values; Type: TABLE DATA; Schema: public; Owner: username
--

INSERT INTO public.collection_values (id, value, collection_id, created_at, updated_at, sort) VALUES (1, 'Козлова Анна Руслановна', 1, '2025-06-29 12:37:28', '2025-06-29 12:37:28', 100);
INSERT INTO public.collection_values (id, value, collection_id, created_at, updated_at, sort) VALUES (2, 'Кожевников Давид Арсентьевич', 1, '2025-06-29 12:37:28', '2025-06-29 12:37:28', 200);
INSERT INTO public.collection_values (id, value, collection_id, created_at, updated_at, sort) VALUES (3, 'Панина Виктория Кирилловна', 1, '2025-06-29 12:37:28', '2025-06-29 12:37:28', 300);
INSERT INTO public.collection_values (id, value, collection_id, created_at, updated_at, sort) VALUES (4, 'Морозова Екатерина Данииловна', 1, '2025-06-29 12:37:28', '2025-06-29 12:37:28', 400);
INSERT INTO public.collection_values (id, value, collection_id, created_at, updated_at, sort) VALUES (5, 'Озеров Владислав Кириллович', 1, '2025-06-29 12:37:28', '2025-06-29 12:37:28', 500);
INSERT INTO public.collection_values (id, value, collection_id, created_at, updated_at, sort) VALUES (6, 'Игнатьева Анна Романовна', 1, '2025-06-29 12:37:28', '2025-06-29 12:37:28', 600);
INSERT INTO public.collection_values (id, value, collection_id, created_at, updated_at, sort) VALUES (7, 'Маслова Варвара Романовна', 1, '2025-06-29 12:37:28', '2025-06-29 12:37:28', 700);
INSERT INTO public.collection_values (id, value, collection_id, created_at, updated_at, sort) VALUES (8, 'визит', 2, '2025-06-29 12:43:23', '2025-06-29 12:43:23', 100);
INSERT INTO public.collection_values (id, value, collection_id, created_at, updated_at, sort) VALUES (9, 'проверка', 2, '2025-06-29 12:43:23', '2025-06-29 12:43:23', 200);
INSERT INTO public.collection_values (id, value, collection_id, created_at, updated_at, sort) VALUES (10, 'мероприятие', 2, '2025-06-29 12:43:23', '2025-06-29 12:43:23', 300);
INSERT INTO public.collection_values (id, value, collection_id, created_at, updated_at, sort) VALUES (11, 'завершено', 3, '2025-06-29 12:43:42', '2025-06-29 12:43:42', 100);
INSERT INTO public.collection_values (id, value, collection_id, created_at, updated_at, sort) VALUES (12, 'отменено', 3, '2025-06-29 12:43:42', '2025-06-29 12:43:42', 200);
INSERT INTO public.collection_values (id, value, collection_id, created_at, updated_at, sort) VALUES (13, 'перенесено', 3, '2025-06-29 12:43:42', '2025-06-29 12:43:42', 300);


--
-- Data for Name: custom_report_types; Type: TABLE DATA; Schema: public; Owner: username
--

INSERT INTO public.custom_report_types (id, title, created_at, updated_at, is_general, attachment_id, is_freelance, command, is_updatable) VALUES (1, 'Импорт базы посещений', '2025-06-29 12:33:46', '2025-06-29 12:33:46', true, NULL, false, NULL, false);
INSERT INTO public.custom_report_types (id, title, created_at, updated_at, is_general, attachment_id, is_freelance, command, is_updatable) VALUES (2, 'Импорт базы мероприятий', '2025-06-29 12:34:22', '2025-06-29 12:34:22', true, NULL, false, NULL, false);


--
-- Data for Name: departament_types; Type: TABLE DATA; Schema: public; Owner: username
--

INSERT INTO public.departament_types (id, name, created_at, updated_at, show_minister_view, sort) VALUES (1, 'Министерство', '2025-06-29 12:32:35', '2025-06-29 12:32:35', true, NULL);


--
-- Data for Name: districts; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: departaments; Type: TABLE DATA; Schema: public; Owner: username
--

INSERT INTO public.departaments (id, name, departament_type_id, created_at, updated_at, district_id, sort, rating, inn, dadata, address, lat, lon, okpo, show_in_dashboard, polygon, parent_id, federation, phone, contact_fullname, email, email_fullname) VALUES (1, 'Демо Учреждение', 1, '2025-06-29 12:32:48', '2025-06-29 12:32:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, true, NULL, NULL, NULL, NULL, NULL, NULL, NULL);


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: username
--

INSERT INTO public.users (id, email, email_verified_at, password, remember_token, created_at, updated_at, permissions, phone, last_name, first_name, middle_name, departament_id, attachment_id, is_active, oid) VALUES (1, 'demo-user@email.com', NULL, '$2y$12$Em2IgirUP5XeTnTCZv2V0uRWntXylPIJHG5I6hXbUXKxzryM/P132', 'K6luiTxpde0YOD94CffjnGXbdqqpKnsVmjw7BFhZZavPyJITaTafplDuCFcu', '2025-06-29 12:30:11', '2025-06-29 12:33:00', '{"platform.index": "1", "platform.min.base": "0", "platform.forms.edit": "1", "platform.forms.list": "1", "platform.events.edit": "1", "platform.events.list": "1", "platform.checker.base": "0", "platform.events.create": "1", "platform.systems.roles": "1", "platform.systems.users": "1", "platform.bot_users.base": "1", "platform.districts.edit": "1", "platform.districts.list": "1", "platform.supervisor.base": "0", "platform.collections.edit": "1", "platform.collections.list": "1", "platform.forms.admin-edit": "1", "platform.departaments.edit": "1", "platform.departaments.list": "1", "platform.form_results.list": "1", "platform.plugins.ibkb.base": "1", "platform.systems.attachment": "1", "platform.custom-reports.base": "1", "platform.form-categories.edit": "1", "platform.form-categories.list": "1", "platform.subdepartaments.base": "1", "platform.custom-reports.loading": "1", "platform.departament-types.edit": "1", "platform.departament-types.list": "1", "platform.departament-director.base": "0", "platform.external-departaments.edit": "1", "platform.external-departaments.list": "1", "platform.plugins.entity-logger.base": "1", "platform.plugins.accounting-vulnerabilities.base": "1"}', '9999999999', 'Иванов', 'Иван', 'Иванович', 1, NULL, true, NULL);


--
-- Data for Name: custom_report_datas; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: custom_reports; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: custom_report_logs; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: custom_report_types_users; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: district_dashboard_params; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: entity_logs; Type: TABLE DATA; Schema: public; Owner: username
--

INSERT INTO public.entity_logs (id, message, model, fields, "user", ip, created_at, updated_at) VALUES (1, '100', 'App\Models\User', '{"phone":"9999999999"}', 'null', '127.0.0.1', '2025-06-29 12:30:11', '2025-06-29 12:30:11');
INSERT INTO public.entity_logs (id, message, model, fields, "user", ip, created_at, updated_at) VALUES (2, '100', 'App\Models\User', '{"phone":"9999999999","updated_at":"2025-06-29T09:30:11.000000Z","created_at":"2025-06-29T09:30:11.000000Z","id":1}', 'null', '127.0.0.1', '2025-06-29 12:30:11', '2025-06-29 12:30:11');
INSERT INTO public.entity_logs (id, message, model, fields, "user", ip, created_at, updated_at) VALUES (3, '100', 'App\Models\User', '{"id":1,"email":null,"email_verified_at":null,"created_at":"2025-06-29 12:30:11","updated_at":"2025-06-29 12:30:11","phone":"9999999999","last_name":null,"first_name":null,"middle_name":null,"departament_id":null,"attachment_id":null,"is_active":true,"oid":null}', '{"id":1}', '5.227.42.29', '2025-06-29 12:30:24', '2025-06-29 12:30:24');
INSERT INTO public.entity_logs (id, message, model, fields, "user", ip, created_at, updated_at) VALUES (4, '100', 'App\Models\User', '{"id":1,"email":"demo-user@email.com","email_verified_at":null,"created_at":"2025-06-29T09:30:11.000000Z","updated_at":"2025-06-29T09:30:11.000000Z","phone":"9999999999","last_name":"Иванов","first_name":"Иван","middle_name":"Иванович","departament_id":null,"attachment_id":null,"is_active":"1","oid":null}', '{"id":1}', '5.227.42.29', '2025-06-29 12:32:01', '2025-06-29 12:32:01');
INSERT INTO public.entity_logs (id, message, model, fields, "user", ip, created_at, updated_at) VALUES (5, '100', 'App\Models\User', '{"id":1,"email":"demo-user@email.com","email_verified_at":null,"created_at":"2025-06-29T09:30:11.000000Z","updated_at":"2025-06-29T09:32:01.000000Z","phone":"9999999999","last_name":"Иванов","first_name":"Иван","middle_name":"Иванович","departament_id":null,"attachment_id":null,"is_active":"1","oid":null}', '{"id":1}', '5.227.42.29', '2025-06-29 12:32:09', '2025-06-29 12:32:09');
INSERT INTO public.entity_logs (id, message, model, fields, "user", ip, created_at, updated_at) VALUES (6, '100', 'App\Models\User', '{"id":1,"email":"demo-user@email.com","email_verified_at":null,"created_at":"2025-06-29T09:30:11.000000Z","updated_at":"2025-06-29T09:32:09.000000Z","phone":"9999999999","last_name":"Иванов","first_name":"Иван","middle_name":"Иванович","departament_id":"1","attachment_id":null,"is_active":"1","oid":null}', '{"id":1}', '5.227.42.29', '2025-06-29 12:33:00', '2025-06-29 12:33:00');
INSERT INTO public.entity_logs (id, message, model, fields, "user", ip, created_at, updated_at) VALUES (7, '100', 'App\Models\Form', '{"name":"Беза посещений","type":"200","periodicity":"50","deadline":null,"form_category_id":null,"sort":null,"is_active":"1","is_editable":"0","by_initiative":"0","requires_approval":"0"}', '{"id":1}', '5.227.42.29', '2025-06-29 12:34:48', '2025-06-29 12:34:48');
INSERT INTO public.entity_logs (id, message, model, fields, "user", ip, created_at, updated_at) VALUES (8, '100', 'App\Models\Form', '{"name":"Беза посещений","type":"200","periodicity":"50","deadline":null,"form_category_id":null,"sort":null,"is_active":"1","is_editable":"0","by_initiative":"0","requires_approval":"0","updated_at":"2025-06-29T09:34:48.000000Z","created_at":"2025-06-29T09:34:48.000000Z","id":1}', '{"id":1}', '5.227.42.29', '2025-06-29 12:34:48', '2025-06-29 12:34:48');
INSERT INTO public.entity_logs (id, message, model, fields, "user", ip, created_at, updated_at) VALUES (9, '100', 'App\Models\Form', '{"id":1,"name":"Беза посещений","periodicity":"50","periodicity_step":null,"deadline":null,"type":"200","is_active":"1","is_editable":"0","created_at":"2025-06-29T09:34:48.000000Z","updated_at":"2025-06-29T09:34:48.000000Z","form_category_id":null,"sort":null,"by_initiative":"0","requires_approval":"0"}', '{"id":1}', '5.227.42.29', '2025-06-29 12:35:56', '2025-06-29 12:35:56');
INSERT INTO public.entity_logs (id, message, model, fields, "user", ip, created_at, updated_at) VALUES (10, '100', 'App\Models\Form', '{"id":1,"name":"Беза посещений","periodicity":"50","periodicity_step":null,"deadline":null,"type":"200","is_active":"1","is_editable":"0","created_at":"2025-06-29T09:34:48.000000Z","updated_at":"2025-06-29T09:35:56.000000Z","form_category_id":null,"sort":null,"by_initiative":"0","requires_approval":"0"}', '{"id":1}', '5.227.42.29', '2025-06-29 12:36:17', '2025-06-29 12:36:17');
INSERT INTO public.entity_logs (id, message, model, fields, "user", ip, created_at, updated_at) VALUES (11, '100', 'App\Models\Form', '{"id":1,"name":"Беза посещений","periodicity":"50","periodicity_step":null,"deadline":null,"type":"200","is_active":"1","is_editable":"0","created_at":"2025-06-29T09:34:48.000000Z","updated_at":"2025-06-29T09:36:17.000000Z","form_category_id":null,"sort":null,"by_initiative":"0","requires_approval":"0"}', '{"id":1}', '5.227.42.29', '2025-06-29 12:37:48', '2025-06-29 12:37:48');
INSERT INTO public.entity_logs (id, message, model, fields, "user", ip, created_at, updated_at) VALUES (12, '100', 'App\Models\Form', '{"id":1,"name":"Беза посещений","periodicity":"50","periodicity_step":null,"deadline":null,"type":"200","is_active":"1","is_editable":"0","created_at":"2025-06-29T09:34:48.000000Z","updated_at":"2025-06-29T09:37:48.000000Z","form_category_id":null,"sort":null,"by_initiative":"0","requires_approval":"0"}', '{"id":1}', '5.227.42.29', '2025-06-29 12:38:00', '2025-06-29 12:38:00');
INSERT INTO public.entity_logs (id, message, model, fields, "user", ip, created_at, updated_at) VALUES (13, '100', 'App\Models\Form', '{"id":1,"name":"Беза посещений","periodicity":"50","periodicity_step":null,"deadline":null,"type":"200","is_active":"1","is_editable":"0","created_at":"2025-06-29T09:34:48.000000Z","updated_at":"2025-06-29T09:38:00.000000Z","form_category_id":null,"sort":null,"by_initiative":"0","requires_approval":"0"}', '{"id":1}', '5.227.42.29', '2025-06-29 12:38:11', '2025-06-29 12:38:11');
INSERT INTO public.entity_logs (id, message, model, fields, "user", ip, created_at, updated_at) VALUES (14, '100', 'App\Models\Form', '{"id":1,"name":"Беза посещений","periodicity":"50","periodicity_step":null,"deadline":null,"type":"200","is_active":"1","is_editable":"0","created_at":"2025-06-29T09:34:48.000000Z","updated_at":"2025-06-29T09:38:11.000000Z","form_category_id":null,"sort":null,"by_initiative":"0","requires_approval":"0"}', '{"id":1}', '5.227.42.29', '2025-06-29 12:38:45', '2025-06-29 12:38:45');
INSERT INTO public.entity_logs (id, message, model, fields, "user", ip, created_at, updated_at) VALUES (15, '100', 'App\Models\Form', '{"id":1,"name":"Беза посещений","periodicity":"50","periodicity_step":null,"deadline":null,"type":"200","is_active":"1","is_editable":"0","created_at":"2025-06-29T09:34:48.000000Z","updated_at":"2025-06-29T09:38:45.000000Z","form_category_id":null,"sort":null,"by_initiative":"0","requires_approval":"0"}', '{"id":1}', '5.227.42.29', '2025-06-29 12:41:06', '2025-06-29 12:41:06');
INSERT INTO public.entity_logs (id, message, model, fields, "user", ip, created_at, updated_at) VALUES (16, '100', 'App\Models\Form', '{"id":1,"name":"Беза посещений","periodicity":"50","periodicity_step":null,"deadline":null,"type":"200","is_active":"1","is_editable":"0","created_at":"2025-06-29T09:34:48.000000Z","updated_at":"2025-06-29T09:41:06.000000Z","form_category_id":null,"sort":null,"by_initiative":"0","requires_approval":"0"}', '{"id":1}', '5.227.42.29', '2025-06-29 12:41:53', '2025-06-29 12:41:53');
INSERT INTO public.entity_logs (id, message, model, fields, "user", ip, created_at, updated_at) VALUES (17, '100', 'App\Models\Form', '{"id":1,"name":"Беза посещений","periodicity":"50","periodicity_step":null,"deadline":null,"type":"200","is_active":"1","is_editable":"0","created_at":"2025-06-29T09:34:48.000000Z","updated_at":"2025-06-29T09:41:53.000000Z","form_category_id":null,"sort":null,"by_initiative":"0","requires_approval":"0"}', '{"id":1}', '5.227.42.29', '2025-06-29 12:42:39', '2025-06-29 12:42:39');
INSERT INTO public.entity_logs (id, message, model, fields, "user", ip, created_at, updated_at) VALUES (18, '100', 'App\Models\Form', '{"id":1,"name":"Беза посещений","periodicity":"50","periodicity_step":null,"deadline":null,"type":"200","is_active":"1","is_editable":"0","created_at":"2025-06-29T09:34:48.000000Z","updated_at":"2025-06-29T09:42:39.000000Z","form_category_id":null,"sort":null,"by_initiative":"0","requires_approval":"0"}', '{"id":1}', '5.227.42.29', '2025-06-29 12:44:51', '2025-06-29 12:44:51');
INSERT INTO public.entity_logs (id, message, model, fields, "user", ip, created_at, updated_at) VALUES (19, '100', 'App\Models\Form', '{"id":1,"name":"База посещений","periodicity":"50","periodicity_step":null,"deadline":null,"type":"200","is_active":"1","is_editable":"0","created_at":"2025-06-29T09:34:48.000000Z","updated_at":"2025-06-29T09:44:51.000000Z","form_category_id":null,"sort":null,"by_initiative":"0","requires_approval":"0"}', '{"id":1}', '5.227.42.29', '2025-06-29 12:45:23', '2025-06-29 12:45:23');


--
-- Data for Name: form_categories; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: forms; Type: TABLE DATA; Schema: public; Owner: username
--

INSERT INTO public.forms (id, name, periodicity, periodicity_step, deadline, type, is_active, is_editable, created_at, updated_at, form_category_id, sort, by_initiative, requires_approval) VALUES (1, 'База посещений', 50, NULL, NULL, 200, true, false, '2025-06-29 12:34:48', '2025-06-29 12:45:23', NULL, NULL, false, false);


--
-- Data for Name: events; Type: TABLE DATA; Schema: public; Owner: username
--

INSERT INTO public.events (id, form_id, departament_id, form_structure, filled_at, refilled_at, created_at, updated_at, saved_structure, prepared_at, user_id, changing_filled_at, approval_departament_id) VALUES (1, 1, 1, '{"form":{"id":1,"name":"База посещений","periodicity":50,"periodicity_step":null,"deadline":null,"type":200,"is_active":true,"is_editable":false,"created_at":"2025-06-29T09:34:48.000000Z","updated_at":"2025-06-29T09:45:23.000000Z","form_category_id":null,"sort":null,"by_initiative":false,"requires_approval":false},"fields":[{"id":4,"form_id":1,"name":"Код региона","group":null,"type":500,"sort":300,"collection_id":null,"created_at":"2025-06-29T09:38:45.000000Z","updated_at":"2025-06-29T09:42:39.000000Z","checker_user_id":null,"group_id":2,"event_key":null},{"id":5,"form_id":1,"name":"Ответственный","group":null,"type":200,"sort":400,"collection_id":1,"created_at":"2025-06-29T09:41:06.000000Z","updated_at":"2025-06-29T09:42:39.000000Z","checker_user_id":null,"group_id":2,"event_key":null},{"id":6,"form_id":1,"name":"Дата","group":null,"type":400,"sort":600,"collection_id":null,"created_at":"2025-06-29T09:42:39.000000Z","updated_at":"2025-06-29T09:42:39.000000Z","checker_user_id":null,"group_id":3,"event_key":null},{"id":7,"form_id":1,"name":"ФИО посетителя","group":null,"type":100,"sort":700,"collection_id":null,"created_at":"2025-06-29T09:42:39.000000Z","updated_at":"2025-06-29T09:42:39.000000Z","checker_user_id":null,"group_id":3,"event_key":null},{"id":8,"form_id":1,"name":"Цель","group":null,"type":300,"sort":800,"collection_id":2,"created_at":"2025-06-29T09:44:51.000000Z","updated_at":"2025-06-29T09:44:51.000000Z","checker_user_id":null,"group_id":3,"event_key":null},{"id":9,"form_id":1,"name":"Статус","group":null,"type":200,"sort":900,"collection_id":3,"created_at":"2025-06-29T09:44:51.000000Z","updated_at":"2025-06-29T09:44:51.000000Z","checker_user_id":null,"group_id":3,"event_key":null},{"id":10,"form_id":1,"name":"Фотографии","group":null,"type":700,"sort":1000,"collection_id":null,"created_at":"2025-06-29T09:44:51.000000Z","updated_at":"2025-06-29T09:44:51.000000Z","checker_user_id":null,"group_id":null,"event_key":null}],"groups":[{"id":1,"name":"Основные узлы структуры","slug":"1751190319098","sort":100,"form_id":1,"parent_id":null,"created_at":"2025-06-29T09:35:56.000000Z","updated_at":"2025-06-29T09:45:23.000000Z","is_multiple":false},{"id":2,"name":"Регион","slug":"1751190319098","sort":200,"form_id":1,"parent_id":1,"created_at":"2025-06-29T09:36:17.000000Z","updated_at":"2025-06-29T09:45:23.000000Z","is_multiple":false},{"id":3,"name":"Посещение","slug":"1751190319098","sort":500,"form_id":1,"parent_id":null,"created_at":"2025-06-29T09:41:53.000000Z","updated_at":"2025-06-29T09:45:23.000000Z","is_multiple":false}],"blockeds":[],"collections":[{"id":1,"name":"Ответственные лица Учреждений","created_at":"2025-06-29T09:37:28.000000Z","updated_at":"2025-06-29T09:37:28.000000Z"},{"id":2,"name":"Цель посещения","created_at":"2025-06-29T09:43:23.000000Z","updated_at":"2025-06-29T09:43:23.000000Z"},{"id":3,"name":"Статус посещения","created_at":"2025-06-29T09:43:42.000000Z","updated_at":"2025-06-29T09:43:42.000000Z"}],"collectionValues":[{"id":1,"value":"Козлова Анна Руслановна","collection_id":1,"created_at":"2025-06-29T09:37:28.000000Z","updated_at":"2025-06-29T09:37:28.000000Z","sort":100},{"id":2,"value":"Кожевников Давид Арсентьевич","collection_id":1,"created_at":"2025-06-29T09:37:28.000000Z","updated_at":"2025-06-29T09:37:28.000000Z","sort":200},{"id":3,"value":"Панина Виктория Кирилловна","collection_id":1,"created_at":"2025-06-29T09:37:28.000000Z","updated_at":"2025-06-29T09:37:28.000000Z","sort":300},{"id":4,"value":"Морозова Екатерина Данииловна","collection_id":1,"created_at":"2025-06-29T09:37:28.000000Z","updated_at":"2025-06-29T09:37:28.000000Z","sort":400},{"id":5,"value":"Озеров Владислав Кириллович","collection_id":1,"created_at":"2025-06-29T09:37:28.000000Z","updated_at":"2025-06-29T09:37:28.000000Z","sort":500},{"id":6,"value":"Игнатьева Анна Романовна","collection_id":1,"created_at":"2025-06-29T09:37:28.000000Z","updated_at":"2025-06-29T09:37:28.000000Z","sort":600},{"id":7,"value":"Маслова Варвара Романовна","collection_id":1,"created_at":"2025-06-29T09:37:28.000000Z","updated_at":"2025-06-29T09:37:28.000000Z","sort":700},{"id":8,"value":"визит","collection_id":2,"created_at":"2025-06-29T09:43:23.000000Z","updated_at":"2025-06-29T09:43:23.000000Z","sort":100},{"id":9,"value":"проверка","collection_id":2,"created_at":"2025-06-29T09:43:23.000000Z","updated_at":"2025-06-29T09:43:23.000000Z","sort":200},{"id":10,"value":"мероприятие","collection_id":2,"created_at":"2025-06-29T09:43:23.000000Z","updated_at":"2025-06-29T09:43:23.000000Z","sort":300},{"id":11,"value":"завершено","collection_id":3,"created_at":"2025-06-29T09:43:42.000000Z","updated_at":"2025-06-29T09:43:42.000000Z","sort":100},{"id":12,"value":"отменено","collection_id":3,"created_at":"2025-06-29T09:43:42.000000Z","updated_at":"2025-06-29T09:43:42.000000Z","sort":200},{"id":13,"value":"перенесено","collection_id":3,"created_at":"2025-06-29T09:43:42.000000Z","updated_at":"2025-06-29T09:43:42.000000Z","sort":300}]}', NULL, NULL, '2025-06-29 12:45:34', '2025-06-29 12:46:10', '"{\"root\":{\"fields\":{},\"childs\":{}}}"', NULL, 1, NULL, NULL);


--
-- Data for Name: external_departaments; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: form_groups; Type: TABLE DATA; Schema: public; Owner: username
--

INSERT INTO public.form_groups (id, name, slug, sort, form_id, parent_id, created_at, updated_at, is_multiple) VALUES (1, 'Основные узлы структуры', '1751190319098', 100, 1, NULL, '2025-06-29 12:35:56', '2025-06-29 12:45:23', false);
INSERT INTO public.form_groups (id, name, slug, sort, form_id, parent_id, created_at, updated_at, is_multiple) VALUES (2, 'Регион', '1751190319098', 200, 1, 1, '2025-06-29 12:36:17', '2025-06-29 12:45:23', false);
INSERT INTO public.form_groups (id, name, slug, sort, form_id, parent_id, created_at, updated_at, is_multiple) VALUES (3, 'Посещение', '1751190319098', 500, 1, NULL, '2025-06-29 12:41:53', '2025-06-29 12:45:23', false);


--
-- Data for Name: fields; Type: TABLE DATA; Schema: public; Owner: username
--

INSERT INTO public.fields (id, form_id, name, "group", type, sort, collection_id, created_at, updated_at, checker_user_id, group_id, event_key) VALUES (4, 1, 'Код региона', NULL, 500, 300, NULL, '2025-06-29 12:38:45', '2025-06-29 12:42:39', NULL, 2, NULL);
INSERT INTO public.fields (id, form_id, name, "group", type, sort, collection_id, created_at, updated_at, checker_user_id, group_id, event_key) VALUES (5, 1, 'Ответственный', NULL, 200, 400, 1, '2025-06-29 12:41:06', '2025-06-29 12:42:39', NULL, 2, NULL);
INSERT INTO public.fields (id, form_id, name, "group", type, sort, collection_id, created_at, updated_at, checker_user_id, group_id, event_key) VALUES (6, 1, 'Дата', NULL, 400, 600, NULL, '2025-06-29 12:42:39', '2025-06-29 12:42:39', NULL, 3, NULL);
INSERT INTO public.fields (id, form_id, name, "group", type, sort, collection_id, created_at, updated_at, checker_user_id, group_id, event_key) VALUES (7, 1, 'ФИО посетителя', NULL, 100, 700, NULL, '2025-06-29 12:42:39', '2025-06-29 12:42:39', NULL, 3, NULL);
INSERT INTO public.fields (id, form_id, name, "group", type, sort, collection_id, created_at, updated_at, checker_user_id, group_id, event_key) VALUES (8, 1, 'Цель', NULL, 300, 800, 2, '2025-06-29 12:44:51', '2025-06-29 12:44:51', NULL, 3, NULL);
INSERT INTO public.fields (id, form_id, name, "group", type, sort, collection_id, created_at, updated_at, checker_user_id, group_id, event_key) VALUES (9, 1, 'Статус', NULL, 200, 900, 3, '2025-06-29 12:44:51', '2025-06-29 12:44:51', NULL, 3, NULL);
INSERT INTO public.fields (id, form_id, name, "group", type, sort, collection_id, created_at, updated_at, checker_user_id, group_id, event_key) VALUES (10, 1, 'Фотографии', NULL, 700, 1000, NULL, '2025-06-29 12:44:51', '2025-06-29 12:44:51', NULL, NULL, NULL);


--
-- Data for Name: form_checker; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: form_checker_results; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: form_departament_types; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: form_field_blockeds; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: form_results; Type: TABLE DATA; Schema: public; Owner: username
--

INSERT INTO public.form_results (id, user_id, event_id, field_id, value, created_at, updated_at, index, saved_structure) VALUES (45, 1, 1, 4, '53', '2025-06-29 12:47:45', '2025-06-29 12:47:45', 0, NULL);
INSERT INTO public.form_results (id, user_id, event_id, field_id, value, created_at, updated_at, index, saved_structure) VALUES (46, 1, 1, 4, '60', '2025-06-29 12:47:45', '2025-06-29 12:47:45', 1, NULL);
INSERT INTO public.form_results (id, user_id, event_id, field_id, value, created_at, updated_at, index, saved_structure) VALUES (47, 1, 1, 4, '44', '2025-06-29 12:47:45', '2025-06-29 12:47:45', 2, NULL);
INSERT INTO public.form_results (id, user_id, event_id, field_id, value, created_at, updated_at, index, saved_structure) VALUES (48, 1, 1, 5, '2', '2025-06-29 12:47:45', '2025-06-29 12:47:45', 0, NULL);
INSERT INTO public.form_results (id, user_id, event_id, field_id, value, created_at, updated_at, index, saved_structure) VALUES (49, 1, 1, 5, '7', '2025-06-29 12:47:45', '2025-06-29 12:47:45', 1, NULL);
INSERT INTO public.form_results (id, user_id, event_id, field_id, value, created_at, updated_at, index, saved_structure) VALUES (50, 1, 1, 5, '1', '2025-06-29 12:47:45', '2025-06-29 12:47:45', 2, NULL);
INSERT INTO public.form_results (id, user_id, event_id, field_id, value, created_at, updated_at, index, saved_structure) VALUES (51, 1, 1, 6, '2025-06-11', '2025-06-29 12:47:45', '2025-06-29 12:47:45', 0, NULL);
INSERT INTO public.form_results (id, user_id, event_id, field_id, value, created_at, updated_at, index, saved_structure) VALUES (52, 1, 1, 6, '2025-06-03', '2025-06-29 12:47:45', '2025-06-29 12:47:45', 1, NULL);
INSERT INTO public.form_results (id, user_id, event_id, field_id, value, created_at, updated_at, index, saved_structure) VALUES (53, 1, 1, 6, '2025-06-30', '2025-06-29 12:47:45', '2025-06-29 12:47:45', 2, NULL);
INSERT INTO public.form_results (id, user_id, event_id, field_id, value, created_at, updated_at, index, saved_structure) VALUES (54, 1, 1, 7, 'Иванов Иван Иванович', '2025-06-29 12:47:45', '2025-06-29 12:47:45', 0, NULL);
INSERT INTO public.form_results (id, user_id, event_id, field_id, value, created_at, updated_at, index, saved_structure) VALUES (55, 1, 1, 7, 'Борисова Варвара Кирилловна', '2025-06-29 12:47:45', '2025-06-29 12:47:45', 1, NULL);
INSERT INTO public.form_results (id, user_id, event_id, field_id, value, created_at, updated_at, index, saved_structure) VALUES (56, 1, 1, 7, 'Субботин Сергей Мартинович', '2025-06-29 12:47:45', '2025-06-29 12:47:45', 2, NULL);
INSERT INTO public.form_results (id, user_id, event_id, field_id, value, created_at, updated_at, index, saved_structure) VALUES (57, 1, 1, 8, '8', '2025-06-29 12:47:45', '2025-06-29 12:47:45', 0, NULL);
INSERT INTO public.form_results (id, user_id, event_id, field_id, value, created_at, updated_at, index, saved_structure) VALUES (58, 1, 1, 8, '9', '2025-06-29 12:47:45', '2025-06-29 12:47:45', 1, NULL);
INSERT INTO public.form_results (id, user_id, event_id, field_id, value, created_at, updated_at, index, saved_structure) VALUES (59, 1, 1, 8, '10', '2025-06-29 12:47:45', '2025-06-29 12:47:45', 2, NULL);
INSERT INTO public.form_results (id, user_id, event_id, field_id, value, created_at, updated_at, index, saved_structure) VALUES (60, 1, 1, 9, '11', '2025-06-29 12:47:45', '2025-06-29 12:47:45', 0, NULL);
INSERT INTO public.form_results (id, user_id, event_id, field_id, value, created_at, updated_at, index, saved_structure) VALUES (61, 1, 1, 9, '12', '2025-06-29 12:47:45', '2025-06-29 12:47:45', 1, NULL);
INSERT INTO public.form_results (id, user_id, event_id, field_id, value, created_at, updated_at, index, saved_structure) VALUES (62, 1, 1, 9, '13', '2025-06-29 12:47:45', '2025-06-29 12:47:45', 2, NULL);


--
-- Data for Name: ibkb_information_systems; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: ibkb_articles; Type: TABLE DATA; Schema: public; Owner: username
--

INSERT INTO public.ibkb_articles (id, title, slug, content, parent_id, author_id, system_id, status, tags, created_at, updated_at) VALUES (1, 'Lorem Ipsum', 'c76f89e35263dfd152e89b3104540540', '<p class="ql-align-justify"><strong style="background-color: rgb(255, 255, 255); color: rgb(0, 0, 0);">Lorem Ipsum</strong><span style="background-color: rgb(255, 255, 255); color: rgb(0, 0, 0);">&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</span></p>', NULL, 1, NULL, 100, 'lorem-ipsum', '2025-06-29 12:48:49', '2025-06-29 12:48:49');
INSERT INTO public.ibkb_articles (id, title, slug, content, parent_id, author_id, system_id, status, tags, created_at, updated_at) VALUES (2, 'Why do we use it?', 'f99feb6bd6b659a0f08a071f9a1c6b7c', '<p class="ql-align-justify"><span style="background-color: rgb(255, 255, 255); color: rgb(0, 0, 0);">Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.</span></p><p class="ql-align-justify"><span style="background-color: rgb(255, 255, 255); color: rgb(0, 0, 0);">The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.</span></p>', 1, 1, NULL, 100, 'lorem-ipsum;why-do-we-use-it?', '2025-06-29 12:49:24', '2025-06-29 12:49:24');
INSERT INTO public.ibkb_articles (id, title, slug, content, parent_id, author_id, system_id, status, tags, created_at, updated_at) VALUES (3, 'Where can I get some?', 'f60e904ead12dfad71efe1974516bbc5', '<p class="ql-align-justify"><span style="background-color: rgb(255, 255, 255); color: rgb(0, 0, 0);">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don''t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn''t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.</span></p><p><br></p>', NULL, 1, NULL, 100, 'lorem-ipsum', '2025-06-29 12:50:03', '2025-06-29 12:50:03');
INSERT INTO public.ibkb_articles (id, title, slug, content, parent_id, author_id, system_id, status, tags, created_at, updated_at) VALUES (4, 'Where does it come from?', '5cfcbb05f48cf2114ee43b8cd883789c', '<h2>Why do we use it?</h2><p class="ql-align-justify">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using ''Content here, content here'', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for ''lorem ipsum'' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p><p><br></p><h2>Where does it come from?</h2><p class="ql-align-justify">Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.</p><p class="ql-align-justify">The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.</p><h2><span style="background-color: rgb(255, 255, 255); color: rgb(0, 0, 0);">Where can I get some?</span></h2><p class="ql-align-justify"><span style="background-color: rgb(255, 255, 255); color: rgb(0, 0, 0);">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don''t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn''t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.</span></p>', 3, 1, NULL, 100, 'lorem-ipsum', '2025-06-29 12:50:30', '2025-06-29 12:50:30');


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: notifications; Type: TABLE DATA; Schema: public; Owner: username
--

INSERT INTO public.notifications (id, type, notifiable_type, notifiable_id, data, read_at, created_at, updated_at) VALUES ('50640946-d23f-4e28-9700-5940d9aea175', 'Orchid\Platform\Notifications\DashboardMessage', 'App\Models\User', 1, '{"time":"2025-06-29T09:45:34.940090Z","type":"default","title":"\u041d\u043e\u0432\u044b\u0439 \u043e\u0442\u0447\u0435\u0442","message":"\u0414\u043e\u0431\u0430\u0432\u043b\u0435\u043d \u043d\u043e\u0432\u044b\u0439 \u043e\u0442\u0447\u0435\u0442 \u043d\u0430 \u0437\u0430\u043f\u043e\u043b\u043d\u0435\u043d\u0438\u0435 \"\u0411\u0430\u0437\u0430 \u043f\u043e\u0441\u0435\u0449\u0435\u043d\u0438\u0439\""}', NULL, '2025-06-29 12:45:34', '2025-06-29 12:45:34');


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: prepared_events; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: prepared_form_results; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: username
--

INSERT INTO public.roles (id, slug, name, permissions, created_at, updated_at) VALUES (1, 'departament-worker', 'Сотрудник учреждения', '{"platform.systems.attachment": 1}', '2025-06-29 12:30:55', '2025-06-29 12:30:55');
INSERT INTO public.roles (id, slug, name, permissions, created_at, updated_at) VALUES (2, 'departament-worker-cr', 'Сотрудник учреждения (кастомные отчеты)', '{"platform.systems.attachment": 1, "platform.custom-reports.loading": 1}', '2025-06-29 12:30:55', '2025-06-29 12:30:55');
INSERT INTO public.roles (id, slug, name, permissions, created_at, updated_at) VALUES (3, 'departament-director', 'Директор учреждения', '{"platform.systems.attachment": 1, "platform.departament-director.base": 1}', '2025-06-29 12:30:55', '2025-06-29 12:30:55');
INSERT INTO public.roles (id, slug, name, permissions, created_at, updated_at) VALUES (4, 'min', 'Министр', '{"platform.min.base": 1, "platform.systems.attachment": 1}', '2025-06-29 12:30:55', '2025-06-29 12:30:55');
INSERT INTO public.roles (id, slug, name, permissions, created_at, updated_at) VALUES (5, 'checker', 'Проверяющий', '{"platform.checker.base": 1, "platform.systems.attachment": 1}', '2025-06-29 12:30:55', '2025-06-29 12:30:55');
INSERT INTO public.roles (id, slug, name, permissions, created_at, updated_at) VALUES (6, 'supervisor', 'Руководитель', '{"platform.supervisor.base": 1, "platform.systems.attachment": 1}', '2025-06-29 12:30:55', '2025-06-29 12:30:55');
INSERT INTO public.roles (id, slug, name, permissions, created_at, updated_at) VALUES (7, 'admin', 'Админ', '{"platform.index": true, "platform.min.base": true, "platform.forms.edit": true, "platform.forms.list": true, "platform.events.edit": true, "platform.events.list": true, "platform.checker.base": true, "platform.events.create": true, "platform.systems.roles": true, "platform.systems.users": true, "platform.bot_users.base": true, "platform.districts.edit": true, "platform.districts.list": true, "platform.supervisor.base": true, "platform.collections.edit": true, "platform.collections.list": true, "platform.forms.admin-edit": true, "platform.departaments.edit": true, "platform.departaments.list": true, "platform.form_results.list": true, "platform.plugins.ibkb.base": true, "platform.systems.attachment": true, "platform.custom-reports.base": true, "platform.form-categories.edit": true, "platform.form-categories.list": true, "platform.subdepartaments.base": true, "platform.custom-reports.loading": true, "platform.departament-types.edit": true, "platform.departament-types.list": true, "platform.departament-director.base": true, "platform.external-departaments.edit": true, "platform.external-departaments.list": true, "platform.plugins.entity-logger.base": true, "platform.plugins.accounting-vulnerabilities.base": true}', '2025-06-29 12:30:55', '2025-06-29 12:30:55');
INSERT INTO public.roles (id, slug, name, permissions, created_at, updated_at) VALUES (8, 'demo', 'Демо', '{"platform.index": "1", "platform.min.base": "0", "platform.forms.edit": "1", "platform.forms.list": "1", "platform.events.edit": "1", "platform.events.list": "1", "platform.checker.base": "0", "platform.events.create": "1", "platform.systems.roles": "1", "platform.systems.users": "1", "platform.bot_users.base": "1", "platform.districts.edit": "1", "platform.districts.list": "1", "platform.supervisor.base": "0", "platform.collections.edit": "1", "platform.collections.list": "1", "platform.forms.admin-edit": "1", "platform.departaments.edit": "1", "platform.departaments.list": "1", "platform.form_results.list": "1", "platform.plugins.ibkb.base": "1", "platform.systems.attachment": "1", "platform.custom-reports.base": "1", "platform.form-categories.edit": "1", "platform.form-categories.list": "1", "platform.subdepartaments.base": "1", "platform.custom-reports.loading": "1", "platform.departament-types.edit": "1", "platform.departament-types.list": "1", "platform.departament-director.base": "0", "platform.external-departaments.edit": "1", "platform.external-departaments.list": "1", "platform.plugins.entity-logger.base": "1", "platform.plugins.accounting-vulnerabilities.base": "1"}', '2025-06-29 12:31:29', '2025-06-29 12:31:29');


--
-- Data for Name: role_users; Type: TABLE DATA; Schema: public; Owner: username
--

INSERT INTO public.role_users (user_id, role_id) VALUES (1, 8);


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: username
--

INSERT INTO public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) VALUES ('BbRhC0eaNOlUPXEbC9x4ZlT04ShYEAKVc6zuiKrn', 1, '5.227.42.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoidTE3RDhmNklXVU9DR3Y4UFY3RmxZN1JvcXhkTVRUT0kyc0hWVm83YSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly84MC44Ny4xOTkuOTc6NjA2MC9ob21lIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE4OiJ0b2FzdF9ub3RpZmljYXRpb24iO2E6MDp7fXM6MTg6ImZsYXNoX25vdGlmaWNhdGlvbiI7YTowOnt9fQ==', 1751190751);


--
-- Data for Name: vulnerabilities; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: vulnerability_event_departamens; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: vulnerability_event_events; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: vulnerability_event_vulnerabilities; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: vulnerability_events; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Data for Name: vulnerability_softs; Type: TABLE DATA; Schema: public; Owner: username
--



--
-- Name: attachmentable_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.attachmentable_id_seq', 1, false);


--
-- Name: attachments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.attachments_id_seq', 1, false);


--
-- Name: bot_user_notifications_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.bot_user_notifications_id_seq', 1, false);


--
-- Name: bot_user_questions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.bot_user_questions_id_seq', 1, false);


--
-- Name: bot_users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.bot_users_id_seq', 1, false);


--
-- Name: collection_values_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.collection_values_id_seq', 13, true);


--
-- Name: collections_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.collections_id_seq', 3, true);


--
-- Name: custom_report_datas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.custom_report_datas_id_seq', 1, false);


--
-- Name: custom_report_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.custom_report_logs_id_seq', 1, false);


--
-- Name: custom_report_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.custom_report_types_id_seq', 2, true);


--
-- Name: custom_report_types_users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.custom_report_types_users_id_seq', 1, false);


--
-- Name: custom_reports_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.custom_reports_id_seq', 1, false);


--
-- Name: departament_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.departament_types_id_seq', 1, true);


--
-- Name: departaments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.departaments_id_seq', 1, true);


--
-- Name: district_dashboard_params_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.district_dashboard_params_id_seq', 1, false);


--
-- Name: districts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.districts_id_seq', 1, false);


--
-- Name: entity_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.entity_logs_id_seq', 19, true);


--
-- Name: events_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.events_id_seq', 1, true);


--
-- Name: external_departaments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.external_departaments_id_seq', 1, false);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: fields_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.fields_id_seq', 10, true);


--
-- Name: form_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.form_categories_id_seq', 1, false);


--
-- Name: form_checker_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.form_checker_id_seq', 1, false);


--
-- Name: form_checker_results_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.form_checker_results_id_seq', 1, false);


--
-- Name: form_departament_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.form_departament_types_id_seq', 1, false);


--
-- Name: form_field_blockeds_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.form_field_blockeds_id_seq', 1, false);


--
-- Name: form_groups_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.form_groups_id_seq', 3, true);


--
-- Name: form_results_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.form_results_id_seq', 62, true);


--
-- Name: forms_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.forms_id_seq', 1, true);


--
-- Name: ibkb_articles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.ibkb_articles_id_seq', 4, true);


--
-- Name: ibkb_information_systems_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.ibkb_information_systems_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.migrations_id_seq', 91, true);


--
-- Name: prepared_events_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.prepared_events_id_seq', 1, false);


--
-- Name: prepared_form_results_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.prepared_form_results_id_seq', 1, false);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.roles_id_seq', 8, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.users_id_seq', 1, true);


--
-- Name: vulnerabilities_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.vulnerabilities_id_seq', 1, false);


--
-- Name: vulnerability_event_departamens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.vulnerability_event_departamens_id_seq', 1, false);


--
-- Name: vulnerability_event_events_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.vulnerability_event_events_id_seq', 1, false);


--
-- Name: vulnerability_event_vulnerabilities_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.vulnerability_event_vulnerabilities_id_seq', 1, false);


--
-- Name: vulnerability_events_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.vulnerability_events_id_seq', 1, false);


--
-- Name: vulnerability_softs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: username
--

SELECT pg_catalog.setval('public.vulnerability_softs_id_seq', 1, false);


--
-- PostgreSQL database dump complete
--

