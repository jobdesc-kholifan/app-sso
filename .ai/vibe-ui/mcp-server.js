import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";
import * as cheerio from "cheerio";
import { McpServer } from "@modelcontextprotocol/sdk/server/mcp.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import { z } from "zod";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Directories containing the Vibe UI templates
const UI_KIT_DIR = path.resolve(__dirname, "../../docs-template/ui-kit");
const FORMS_DIR = path.resolve(__dirname, "../../docs-template/forms");

// Create the MCP Server
const server = new McpServer({
  name: "Vibe UI Knowledge Base",
  version: "1.0.0"
});

/**
 * Helper to parse a specific HTML file and extract its documented components.
 * It looks for <section class="doc-section"> elements which contain the examples.
 */
function extractComponentsFromFile(filePath) {
  if (!fs.existsSync(filePath)) {
    throw new Error(`File not found: ${filePath}`);
  }

  const html = fs.readFileSync(filePath, "utf-8");
  const $ = cheerio.load(html);
  
  let result = [];
  
  // Vibe UI uses <section class="doc-section"> for component examples
  $(".doc-section").each((i, el) => {
    const section = $(el);
    const title = section.find(".doc-section-title").text().trim() || `Component Variant ${i + 1}`;
    
    // The description usually follows the title
    let description = "";
    const p = section.find(".doc-section-title").next("p");
    if (p.length) {
      description = p.text().trim();
    }
    
    // We want the actual component HTML. 
    // We remove the title and description from the section clone to get just the raw component.
    const componentHtml = section.clone();
    componentHtml.find(".doc-section-title").remove();
    // Only remove the first <p> if it's the description
    const firstP = componentHtml.find("p").first();
    if (firstP.text().trim() === description) {
        firstP.remove();
    }
    
    // Clean up empty lines and trim
    let rawHtml = componentHtml.html() || "";
    
    // formatting cleanup
    rawHtml = rawHtml.split('\n')
        .map(line => line.trimEnd())
        .filter(line => line.trim() !== '')
        .join('\n');

    result.push(`### ${title}\n${description ? `> ${description}\n` : ''}\n\`\`\`html\n${rawHtml.trim()}\n\`\`\``);
  });

  return result.length > 0 ? result.join("\n\n") : "No structured component examples found in this file.";
}

// Register the tool
server.tool(
  "get_vibe_ui_component",
  "Fetch the exact HTML structure and Tailwind classes for a specific Vibe UI component file.",
  {
    category: z.enum(["ui-kit", "forms"]).describe("The category folder (ui-kit or forms)"),
    filename: z.string().describe("The exact filename without extension (e.g., 'buttons', 'modals', 'form-elements')")
  },
  async ({ category, filename }) => {
    try {
      const dir = category === "forms" ? FORMS_DIR : UI_KIT_DIR;
      const filePath = path.join(dir, `${filename}.html`);
      
      const componentData = extractComponentsFromFile(filePath);
      
      return {
        content: [
          {
            type: "text",
            text: `# Vibe UI: ${filename}\n\n${componentData}`
          }
        ]
      };
    } catch (error) {
      return {
        content: [
          {
            type: "text",
            text: `Error fetching component data: ${error.message}`
          }
        ],
        isError: true
      };
    }
  }
);

// Start the server using stdio transport
async function run() {
  const transport = new StdioServerTransport();
  await server.connect(transport);
  console.error("Vibe UI MCP Server running on stdio");
}

run().catch(console.error);
