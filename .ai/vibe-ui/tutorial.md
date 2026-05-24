# Vibe UI - AI Skill & MCP Server Tutorial

This directory (`.ai/vibe-ui/`) contains the complete machine-readable knowledge base for your Vibe UI templating system. It allows compatible AI agents (like Claude Desktop, Cursor, or AntiGravity) to automatically fetch and write 100% accurate HTML components based on your UI Kit.

## 1. Setup the MCP Server

The Model Context Protocol (MCP) server dynamically parses the HTML files inside your `docs-template` folder and serves the exact HTML structures to the AI.

### Dependencies
The server requires `@modelcontextprotocol/sdk` and `cheerio`. They have already been added to the `package.json` in this directory. If you haven't already, run:
```bash
cd .ai/vibe-ui
npm install
```

### Configuring Claude Desktop
To add this MCP server to Claude Desktop:

1. Open your Claude Desktop config file:
   - Mac: `~/Library/Application Support/Claude/claude_desktop_config.json`
   - Windows: `%APPDATA%\Claude\claude_desktop_config.json`
2. Add the following configuration (replace `/path/to/your/project` with your actual absolute path):
```json
{
  "mcpServers": {
    "vibe-ui": {
      "command": "node",
      "args": ["/Users/kholifanalfon/Sites/app-sso/.ai/vibe-ui/mcp-server.js"]
    }
  }
}
```
3. Restart Claude Desktop. You should now see a little "hammer" icon or the `get_vibe_ui_component` tool available.

## 2. Using the AI Skill (System Prompt)

The `vibe-ui-skill.md` file acts as the instructions for the AI. It tells the AI *how* to use the MCP server and lists all available components.

### How to use in Cursor:
1. Copy the contents of `vibe-ui-skill.md` and paste it into your `.cursorrules` file at the root of your project, OR attach the file as "Project Rules" in Cursor settings.

### How to use in Claude Projects / AntiGravity:
1. Attach `vibe-ui-skill.md` to your Project Knowledge or Custom Instructions.

## 3. Example Prompts

Once configured, you can use natural language prompts like these, and the AI will automatically query the MCP server for the exact code:

> "Build a login form view. Use the Vibe UI form layouts and add a primary button for submission."

> "Create a data table to show users. Make sure it uses the hover variant from Vibe UI tables, and put it inside a Vibe UI Card."

> "I need a modal popup for confirming deletions. Grab the exact HTML structure from the Vibe UI modals component."
