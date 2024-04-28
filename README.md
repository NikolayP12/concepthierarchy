# Concept Hierarchy

## Instalation

The plugin needs to be installed by putting the contents of this directory to

    {your/moodle/dirroot}/mod/data/field/

Afterwards, log in to your Moodle site as an admin and go to /Site administration >
/Notifications to complete the installation.

## License

2024 Nikolay <nikolaypn2002@gmail.com>

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program.
If not, see <https://www.gnu.org/licenses/>.

## Purpose

The purpose of this plugin is to create a new field for the database module that acts as a parent concept in a hierarchical manner. This new field is of the text type, differing from a "short text" field is that it automatically links to a defined "short text" field labeled as "Concept" or "Concepto" to facilitate the linking of Database entries.

This will ease navigation among different Database entries, allowing users to see the parent concept of any given entry, thereby creating an internal hierarchical tree of concepts.

## Configuration

Once the plugin has been installed, to use the icon that identifies the new field, copy the image (without changing its name) located in the pix folder within this project and paste it into:

    {your/moodle/dirroot}/mod/data/pix/field/

With this simple step, once the site cache has been purged, the icon for the new field will appear.

To purge the cache, simply go to /Site administration > /Development > /Purge caches.

## Usage

For the new "Parent Concept" field to function correctly, it is crucial that the "short text" field is named "Concept" or "Concepto". If this is not done, the "Parent Concept" field will not be able to find a "short text" field whose content matches the content entered in the "Parent Concept" field.

## Support

For questions or issues regarding the Concept Hierarchy field, please send an email to the plugin maintainer at nikolaypn2002@gmail.com.
