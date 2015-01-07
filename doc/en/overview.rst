Overview
=============

Nagvis backend and Centreon module
----------------------------------

NagVis and Centreon integration is possible thanks to a NagVis backend (https://forge.centreon.com/projects/centreon-nagvis-backend) that allows Centreon ojects (hosts, services...) to be exposed in NagVis.
With this backend, it is possible to create NagVis maps embedding these Centreon objects with the usual NagVis administration interface.

Then an additionnal Centreon module called "centreon-nagvis" (https://forge.centreon.com/projects/centreon-nagvis) provides the necessary integration to display existing NagVis maps inside Centreon.
.. note:: 
	that map edition is only possible through the usual NagVis UI, there is not map edition in Centreon.

Requirements
------------

+----------------------------+-------------------------+
| **Softwares**              | **Compatible Versions** |
|                            |                         |
+----------------------------+-------------------------+
| **Centreon**               | 2.5.x                   |
|                            |                         |
+----------------------------+-------------------------+
| **Centreon-Nagvis**        | *                       |
|                            |                         |
+----------------------------+-------------------------+
| **Nagvis**                 | 1.7.10                  |
|                            |                         |
+----------------------------+-------------------------+
| **SQLite**                 |                         |
|                            |                         |
+----------------------------+-------------------------+

.. note::

	NagVis must be installed on the Centreon server as the web interface is reading NagVis maps (stored as \*.cfg files).
	Graphviz may also be needed depending on the maps. Please refer to the NagVis installation for more information.

