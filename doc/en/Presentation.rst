Presentation
============

Nagvis backend and Centreon module
----------------------------------

NagVis and Centreon integration is possible thanks to a NagVis backend (https://github.com/centreon/centreon-nagvis-backend) that allows Centreon ojects (hosts, services...) to be exposed in NagVis.
With this backend, it is possible to create NagVis maps embedding these Centreon objects with the usual NagVis administration interface.

Then an additionnal Centreon module called "centreon-nagvis" (https://github.com/centreon/centreon-nagvis) provides the necessary integration to display existing NagVis maps inside Centreon.

.. note:: 

	that map edition is only possible through the usual NagVis UI, there is not map edition in Centreon.

