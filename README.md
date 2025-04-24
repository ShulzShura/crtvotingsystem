# CRT Voting System 🗳️

A capstone project built for the College for Research and Technology, Guimba Campus.  
This system is a **student voting platform** developed using **PHP and MySQL** with help from **ChatGPT** as a learning and coding assistant.

## 📌 Features

- 🧑‍💼 Admin and Voter Login
- 👥 Manage voters, candidates, and admins
- 🗳️ Course-based voting (ACT, FSM, HRS)
- 🕒 Voting open/close schedule
- ✅ Independent voting (optional skip)
- 📊 View and print results
- 🔒 Secure access and logout
- 🖼️ Candidate photos
- 📁 All-in-one file management (no multiple edit pages)

## 🛠️ Technologies Used

- PHP
- MySQL
- HTML/CSS
- Git & GitHub

## 📁 Project Structure

```bash
crtvotingsystem/
├── candidate_photos/      # Folder for storing uploaded candidate photos
├── db_connect.php         # MySQL database connection
├── login.php              # Login page
├── logout.php             # Logout
├── admin_dashboard.php    # Admin home
├── voter_dashboard.php    # Voter home & voting area
├── manage_voters.php      # Add/edit/delete voters
├── manage_candidates.php  # Add/edit/delete candidates
├── manage_admins.php      # Add/edit/delete admins
├── submit_vote.php        # Vote processing script
├── print_results.php      # Printable results
├── schedule.php           # Voting schedule
└── README.md              # This file
