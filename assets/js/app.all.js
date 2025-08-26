                var target = el.get(0);
                if (target && target.nodeType) {
                    this.propertyObserver = new observer(function (mutations) {
                        $.each(mutations, self._sync);
                    });
                    this.propertyObserver.observe(target, { attributes:true, subtree:false });
                }
