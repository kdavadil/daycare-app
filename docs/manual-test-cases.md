# Sibol manual test cases

Use the local test host: http://127.0.0.1:8080/login

Demo PIN: `sibol-local`

## SIB-05 — Parent child home / daily status

### TC-SIB-05-01 — Parent can open linked child home
1. Open the login page.
2. Choose `Parent`.
3. Enter the demo PIN.
4. Submit the form.
5. On the dashboard, open `Maya Dela Cruz`.

Expected result:
- The page title/heading shows `Maya’s day`.
- The page shows `Little Seeds Preschool` and `Sampaguita class`.
- The page shows the latest attendance status, such as `In school`.
- The page shows seeded daily journal items such as `A little artist at work` and `Merienda finished`.

### TC-SIB-05-02 — Parent cannot open staff-only tools
1. While logged in as Parent, manually browse to `/attendance`.
2. Manually browse to `/daily-updates`.
3. Manually browse to `/roster`.

Expected result:
- Each staff-only page returns `403 Forbidden`.
- The parent dashboard and linked child page still work.

## SIB-06 — Teacher learning moments with photo

### TC-SIB-06-01 — Teacher can post an activity note with photo
1. Sign out if needed.
2. Log in as `Teacher` with the demo PIN.
3. Open `Post daily update` from the dashboard.
4. Choose `Maya Dela Cruz`.
5. Choose `Learning moment`.
6. Enter title: `A little artist at work`.
7. Enter a note about the activity.
8. Upload a small image from your computer.
9. Submit the form.

Expected result:
- A success message says the update was posted.
- The update appears in `Recent class updates`.
- Sign out, log in as Parent, open `Maya Dela Cruz`, and confirm the note appears in the daily journal with the photo.

### TC-SIB-06-02 — Teacher cannot post without required fields
1. Log in as Teacher.
2. Open `Post daily update`.
3. Leave `Student` or `Title` blank.
4. Submit the form.

Expected result:
- The page shows validation errors.
- No incomplete journal entry is created.

## SIB-07 — Meals, rest, and care logs

### TC-SIB-07-01 — Teacher can post a meal log
1. Log in as Teacher.
2. Open `Post daily update`.
3. Choose `Maya Dela Cruz`.
4. Choose `Meal`.
5. Enter title: `Merienda finished`.
6. Enter a note.
7. Enter meal amount: `All finished`.
8. Submit the form.

Expected result:
- The update is posted successfully.
- Parent view shows the meal log and the meal amount.

### TC-SIB-07-02 — Meal log requires meal amount
1. Log in as Teacher.
2. Open `Post daily update`.
3. Choose `Meal`.
4. Enter a title and note.
5. Leave `Meal amount` blank.
6. Submit the form.

Expected result:
- The page shows a validation error for meal amount.
- No meal log is created.

### TC-SIB-07-03 — Teacher can post care/rest/mood logs
1. Log in as Teacher.
2. Open `Post daily update`.
3. Choose `Rest / nap`, `Care note`, or `Mood`.
4. Fill in a title and note.
5. Submit the form.

Expected result:
- The update is posted successfully.
- Parent view shows the update in chronological journal order.

## Security spot checks

### TC-SEC-01 — Photo URL requires login
1. As Parent, open a child daily journal entry that has a photo.
2. Open the image in a new tab and copy its URL.
3. Sign out.
4. Paste the image URL again.

Expected result:
- Signed-out access redirects to login or is blocked.
- Signed-in linked parent can view the photo.

### TC-SEC-02 — Parent cannot publish teacher updates
1. Log in as Parent.
2. Browse directly to `/daily-updates`.

Expected result:
- Parent receives `403 Forbidden`.

## Class calendar staff view

### Admin sees all class events
1. Log in with the Admin demo persona using the local demo PIN.
2. From the dashboard, open `View class calendar`.
3. Use Previous and Next to move between months.

Expected result:
- The calendar opens at `/class-calendar`.
- The page shows `Class calendar`, the current month, and seeded events for both Sampaguita and Mango classes when those events fall in the selected month.
- The upcoming event list shows the class name, event type, date, and time where available.

### Teacher sees only assigned class events
1. Log out, then log in with the Teacher demo persona using the local demo PIN.
2. From the dashboard, open `View class calendar`.

Expected result:
- The calendar opens successfully.
- Teacher Ana sees Sampaguita events.
- Teacher Ana does not see Mango-only events.

### Parent cannot open the staff calendar
1. Log out, then log in with the Parent demo persona using the local demo PIN.
2. Manually browse to `/class-calendar`.

Expected result:
- The request is blocked with a forbidden response.
- Parent dashboard and child pages continue to work.


## SIB-09 — Child-specific messages

### TC-SIB-09-01 — Parent can read and send a child message
1. Log in as `Parent` with the demo PIN.
2. Open `Maya Dela Cruz`.
3. Open `Messages with Maya’s teachers`.
4. Read the seeded teacher message.
5. Type a short reply and send it.

Expected result:
- The thread opens at `/children/{id}/messages`.
- Existing messages show sender, role, time, and body.
- The new parent message appears in the thread after submit.

### TC-SIB-09-02 — Assigned teacher can reply
1. Log out, then log in as `Teacher` with the demo PIN.
2. Open `View class roster`.
3. Open messages for `Maya Dela Cruz`.
4. Type a teacher reply and send it.

Expected result:
- Teacher Ana can open and post in Maya’s thread.
- The message is labeled as a teacher message.

### TC-SIB-09-03 — Parent cannot open another child’s thread
1. Log in as `Parent`.
2. Manually browse to another child message URL if known, such as Nico or Lia’s `/children/{id}/messages`.

Expected result:
- The request is blocked with `403 Forbidden`.
- Maya’s own child page and message thread still work.
