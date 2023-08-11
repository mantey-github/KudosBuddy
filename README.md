# Slack Kudos Bot (KudosBuddy)
KudosBuddy (Slack Bot) is a simple Laravel-based api designed to recognize, appreciate, and give credit to team members within your Slack workspace. By allowing team members to share kudos and view scores, the app aims to motivate and encourage a positive work environment.

## Features
- Share kudos with team members.
- View and track kudos scores attained by team members.

## Getting Started
Follow these steps to set up and test the app in your Slack workspace.

### Prerequisites
- PHP > 7.4
- Laravel 10 or higher
- Slack App (create a new app in your Slack workspace)

### Installation
1. Clone the repository to your local machine:
   ```bash
   git clone https://github.com/mantey-github/KudosBuddy.git
   cd KudosBuddy
   ```

2. Install dependencies using Composer:
   ```bash
   composer install
   ```

3. Set up your environment variables by creating a .env file and adding the necessary configurations (e.g., Slack Bot API token, database settings).

4. Generate the application key:
   ```bash
   php artisan key:generate
   ```

5. Run database migrations:
   ```bash
   php artisan migrate
   ```

6. Configure your Slack App:
    - Create a new app in your Slack workspace.
    - Install the app to the desired channel.
    - In your app settings, get the *Bot User OAuth Token* by enabling OAuth & Permissions.
    - Scroll down to *Bot token scopes* and add the following scopes `app_mentions:read`, `chat:write`, `chat:write.customize`, `channels:read`, `channels:history` and `team:read`. These are the permissions the app or bot needs to read and write messages to the Slack channels.
    - Enable and subscribe to Slack events and set the `Request URL` where subscribe events notification will be sent to:
        ```bash
        Request URL: https://your-bot-domain.com/slack/listen
        ```
    Make sure to replace https://your-bot-domain.com with the actual URL where your app is hosted.

### Usage

#### Sharing Kudos
To share a kudos with a team member, send a message to the Slack channel where KudosBuddy is installed:
   ```bash
   Kudos to @team_member_name your_message_here
   ```
For example:
   ```bash
   Kudos to @Joshua Graham for an outstanding job on the new UI designs! 🌟
   ```

#### Viewing Scores
To view the scores attained by team members:
   ```bash
   @app_name_or_bot_name scores
   ```
For example:
   ```bash
   @KudosBuddy scores
   ```

## Contributing
Contributions are welcome! Feel free to submit pull requests or open issues if you encounter any problems or have suggestions for improvements.

